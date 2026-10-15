<?php

/**
 * Verifies the transaction duplicate guard logic without MySQL and without the
 * (locally corrupted) schema grammar: creates the table with raw PDO and uses
 * Eloquent for the model logic.
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

require dirname(__DIR__, 2) . '/vendor/autoload.php';

$dbPath = sys_get_temp_dir() . '/dupguard_' . getmypid() . '.sqlite';
@unlink($dbPath);

$pdo = new PDO('sqlite:' . $dbPath);
$pdo->exec('CREATE TABLE transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    transaction_id TEXT UNIQUE,
    type TEXT,
    status TEXT,
    from_account_id INTEGER,
    to_account_id INTEGER,
    from_user_id INTEGER,
    to_user_id INTEGER,
    amount NUMERIC,
    fee NUMERIC DEFAULT 0,
    net_amount NUMERIC DEFAULT 0,
    currency_id INTEGER,
    reference TEXT UNIQUE,
    memo TEXT,
    idempotency_key TEXT UNIQUE,
    created_at TEXT,
    updated_at TEXT
)');

$capsule = new Capsule;
$capsule->addConnection([
    'driver'   => 'sqlite',
    'database' => $dbPath,
]);
$capsule->setEventDispatcher(new Dispatcher(new Container));
$capsule->setAsGlobal();
$capsule->bootEloquent();
// Eloquent must see the table the raw PDO created.
$capsule->getConnection()->disconnect();

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use App\Support\TransactionIdentifiers;

$pass = 0; $fail = 0;
$check = function (string $label, bool $ok) use (&$pass, &$fail) {
    echo ($ok ? 'PASS' : 'FAIL') . " - $label\n";
    $ok ? $pass++ : $fail++;
};

$make = function (array $overrides = []) {
    return Transaction::create(array_merge([
        'transaction_id' => TransactionIdentifiers::uniqueTransactionId(),
        'type' => 'payment_debit',
        'status' => TransactionStatus::PendingReview->value,
        'from_account_id' => 14,
        'to_account_id' => 14,
        'from_user_id' => 10,
        'to_user_id' => 10,
        'amount' => 5000,
        'reference' => TransactionIdentifiers::uniqueReference('PAY'),
        'idempotency_key' => bin2hex(random_bytes(32)),
    ], $overrides));
};

$base = ['user_id' => 10, 'account_id' => 14, 'type' => 'payment_debit', 'amount' => 5000];
$guard = fn (array $over) => TransactionIdentifiers::recentDuplicate($over);

echo "=== Transaction duplicate guard ===\n";

$check('empty table reports no duplicate', $guard($base) === null);

$original = $make();
$found = $guard($base);
$check('identical pending request is detected', $found !== null && $found->id === $original->id);

$key = 'stable-key-abc123';
$replayed = $make(['idempotency_key' => $key]);
$lookup = TransactionIdentifiers::findForIdempotencyKey($key);
$check('idempotency key resolves to existing transaction', $lookup !== null && $lookup->id === $replayed->id);

$check('different amount is not a duplicate', $guard(array_merge($base, ['amount' => 4300])) === null);
$check('different account is not a duplicate', $guard(array_merge($base, ['account_id' => 99])) === null);
$check('different user is not a duplicate', $guard(array_merge($base, ['user_id' => 77])) === null);
$check('different type is not a duplicate', $guard(array_merge($base, ['type' => 'deposit_credit'])) === null);

// A completed transaction must not block a new identical one.
$make(['idempotency_key' => bin2hex(random_bytes(32)), 'status' => TransactionStatus::Credited->value]);
$pendingOnly = Transaction::where('status', TransactionStatus::PendingReview->value)->first();
$check('completed request does not block a new one', $guard($base)->id === $pendingOnly->id);

// Outside the window the guard must not fire.
Capsule::table('transactions')->update(['created_at' => now()->subMinutes(60)->toDateTimeString()]);
$check('request older than the window is not a duplicate', $guard($base) === null);

$ids = array_map(fn() => TransactionIdentifiers::uniqueTransactionId(), range(1, 25));
$check('generated transaction ids are unique', count(array_unique($ids)) === 25);

$refs = array_map(fn() => TransactionIdentifiers::uniqueReference('PAY'), range(1, 25));
$check('generated references are unique', count(array_unique($refs)) === 25);

@unlink($dbPath);
echo "\n$pass passed, $fail failed\n";
exit($fail === 0 ? 0 : 1);
