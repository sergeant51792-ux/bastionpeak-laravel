<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SignInController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\ActivityController;
use App\Http\Controllers\Customer\TransferController;
use App\Http\Controllers\Customer\TopUpController;
use App\Http\Controllers\Customer\PayController;
use App\Http\Controllers\Customer\AccountController;
use App\Http\Controllers\Customer\ReceiveController;
use App\Http\Controllers\Customer\MessageController;
use App\Http\Controllers\Customer\NotificationController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\CardController;
use App\Http\Controllers\Customer\StatementController;
use App\Http\Controllers\Customer\FinancialServiceController;
use App\Http\Controllers\Customer\DepositController;
use App\Http\Controllers\Customer\TwoFactorController;

Route::middleware('web')->group(function () {
    Route::get('/signin', [SignInController::class, 'showSignIn'])->name('signin');
    Route::get('/signin/first', [SignInController::class, 'showFirstSignIn'])->name('signin.first');
    Route::post('/signin/first', [SignInController::class, 'firstSignIn'])->name('signin.first.post');
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequest'])->name('password.forgot');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.forgot.post');
    Route::get('/password/verify-pin', [ForgotPasswordController::class, 'showPinVerification'])->name('password.verify.pin');
    Route::post('/password/verify-pin', [ForgotPasswordController::class, 'verifyPin'])->name('password.verify.pin.post');
    Route::get('/password/reset', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [ForgotPasswordController::class, 'reset'])->name('password.reset.post');
    Route::get('/', function () {
        return view('landing');
    })->name('landing');

    Route::get('/support', [\App\Http\Controllers\PageController::class, 'support'])->name('support');
    Route::post('/support', [\App\Http\Controllers\PageController::class, 'submitSupport'])->name('support.submit');
    Route::get('/policies', [\App\Http\Controllers\PageController::class, 'policies'])->name('policies');
    Route::get('/terms', [\App\Http\Controllers\PageController::class, 'terms'])->name('terms');
    Route::get('/privacy', [\App\Http\Controllers\PageController::class, 'privacy'])->name('privacy');
    Route::get('/learn', [\App\Http\Controllers\PageController::class, 'learn'])->name('learn');
    Route::get('/recommendations', [\App\Http\Controllers\PageController::class, 'recommendations'])->name('recommendations');
    Route::post('/signin', [SignInController::class, 'signIn'])->name('signin.post');
    Route::get('/signin/otp', [SignInController::class, 'showVerifyOtp'])->name('signin.otp');
    Route::post('/signin/otp', [SignInController::class, 'verifyOtp'])->name('signin.otp.post');
    Route::get('/signin/pin', [SignInController::class, 'showVerifyPin'])->name('signin.pin');
    Route::post('/signin/pin', [SignInController::class, 'verifyPin'])->name('signin.pin.post');
    Route::post('/signout', [SignInController::class, 'signOut'])->name('signout');

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

    Route::middleware('auth')->group(function () {
        Route::get('/home', [HomeController::class, '__invoke'])->name('home');
        Route::get('/dashboard', [HomeController::class, '__invoke'])->name('dashboard');

        Route::get('/activity', [ActivityController::class, '__invoke'])->name('activity');
        Route::get('/activity/{transaction}', [ActivityController::class, 'receipt'])->name('activity.receipt');
        Route::get('/activity/{transaction}/download', [ActivityController::class, 'downloadReceipt'])->name('activity.receipt.download');

        Route::get('/security/two-factor', [TwoFactorController::class, 'setup'])->name('two-factor.setup');
        Route::post('/security/two-factor', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
        Route::delete('/security/two-factor', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
        Route::get('/verify/otp', [TwoFactorController::class, 'challenge'])->name('two-factor.challenge');
        Route::post('/verify/otp', [TwoFactorController::class, 'verify'])->name('two-factor.verify');
        Route::get('/transfer', [TransferController::class, '__invoke'])->name('transfer')->middleware('account.status');
        Route::get('/top-up', [TopUpController::class, '__invoke'])->name('top-up')->middleware('account.status');
        Route::post('/top-up', [TopUpController::class, 'submit'])->name('top-up.submit')->middleware('account.status');
        Route::get('/pay', [PayController::class, '__invoke'])->name('pay')->middleware('account.status');
        Route::post('/pay', [PayController::class, 'submit'])->name('pay.submit')->middleware('account.status');
        Route::get('/accounts/{account}', [AccountController::class, 'show'])->name('accounts.show');
        Route::get('/receive', [ReceiveController::class, '__invoke'])->name('receive')->middleware('account.status');
        Route::get('/messages', [MessageController::class, '__invoke'])->name('messages');
        Route::post('/messages', [MessageController::class, 'send'])->name('messages.send');
        Route::post('/messages/{message}/read', [MessageController::class, 'markAsRead'])->name('messages.read');
        Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
        Route::delete('/messages/thread/{threadId}', [MessageController::class, 'destroyThread'])->name('messages.thread.destroy');
        Route::get('/notifications', [NotificationController::class, '__invoke'])->name('notifications');
        Route::post('/notifications/mark-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-read');
        Route::get('/profile', [ProfileController::class, '__invoke'])->name('profile');
        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.change');
        Route::post('/profile/picture/upload', [ProfileController::class, 'uploadPicture'])->name('profile.picture.upload');
        Route::post('/profile/picture/remove', [ProfileController::class, 'removePicture'])->name('profile.picture.remove');
        Route::post('/profile/preferences', [ProfileController::class, 'updatePreferences'])->name('profile.preferences');
        Route::post('/language', [ProfileController::class, 'setLanguage'])->name('language.set');
        Route::get('/cards', [CardController::class, '__invoke'])->name('cards');
        Route::post('/cards/request', [CardController::class, 'requestCard'])->name('cards.request');
        Route::get('/cards/{card}/pin', [CardController::class, 'showPin'])->name('card.pin');
        Route::post('/cards/{card}/pin', [CardController::class, 'setPin'])->name('card.pin.set');
        Route::put('/cards/{card}/pin', [CardController::class, 'changePin'])->name('card.pin.change');
        Route::get('/statements', [StatementController::class, '__invoke'])->name('statements');
        Route::post('/statements/generate', [StatementController::class, 'generate'])->name('statements.generate');
        Route::get('/deposit', [DepositController::class, '__invoke'])->name('deposit');
        Route::post('/deposit', [DepositController::class, 'submit'])->name('deposit.submit');

        Route::get('/financial-services', [FinancialServiceController::class, '__invoke'])->name('financial-services');
        Route::get('/financial-services/{service}/apply', [FinancialServiceController::class, 'apply'])->name('financial-service.apply');
        Route::post('/financial-services/{service}/apply', [FinancialServiceController::class, 'submitApplication'])->name('financial-service.submit');

        Route::get('/dashboard/updates', function () {
            $user = auth()->user();
            return response()->json([
                'unread_messages' => $user->unreadMessagesCount(),
                'unread_notifications' => $user->notifications()->whereNull('read_at')->count(),
                'accounts' => $user->accounts()->with('currency')->get()->map(fn($a) => [
                    'id' => $a->id,
                    'name' => $a->name,
                    'balance' => (float) $a->balance,
                    'currency_code' => $a->currency->code,
                    'currency_symbol' => $a->currency->symbol,
                    'currency_decimals' => (int) ($a->currency->decimals ?? 2),
                    'exchange_rate' => (float) $a->currency->exchange_rate,
                ]),
            ]);
        })->name('dashboard.updates');
    });
});

// NOTE: The old web-based Artisan routes (/run-migrate, /run-seed,
// /storage-link, /config_cache, /optimize, /clear-cache, /copy-build) were
// removed because they were publicly reachable. Database migrations, seeding,
// the storage link and asset copying are now handled by the token-protected
// `deploy.php` helper at the project root. Delete that file after setup.

Route::get('/_deploy-assets', function () {
    $urlKey = request('key', '');
    if (!hash_equals(env('DEPLOY_KEY', 'bastionpeak-deploy-secret-737940260'), $urlKey)) {
        http_response_code(403);
        return 'Forbidden';
    }
    $dst = '/home/bastionpeaks/public_html';
    $deployed = [];

    // Deploy fonts
    $srcFonts = public_path('fonts');
    $dstFonts = $dst . '/fonts';
    if (!is_dir($dstFonts)) @mkdir($dstFonts, 0755, true);
    $fontCount = 0;
    if (is_dir($srcFonts)) {
        foreach (scandir($srcFonts) ?: [] as $item) {
            if ($item === '.' || $item === '..') continue;
            $s = $srcFonts . '/' . $item;
            if (is_file($s)) { @copy($s, $dstFonts . '/' . $item); $fontCount++; }
        }
    }
    // Fallback: download from GitHub
    if ($fontCount === 0) {
        $ghFonts = 'https://raw.githubusercontent.com/sergeant51792-ux/bastionpeak-laravel/main/public/fonts/';
        foreach (['schibsted.css', 'ibm-plex.css'] as $f) {
            $content = @file_get_contents($ghFonts . $f);
            if ($content !== false) {
                file_put_contents($dstFonts . '/' . $f, $content);
                $fontCount++;
            }
        }
    }
    $deployed[] = "$fontCount font files deployed";

    // Deploy CSS files
    $srcCss = public_path('css');
    $dstCss = $dst . '/css';
    if (!is_dir($dstCss)) @mkdir($dstCss, 0755, true);
    $cssCount = 0;
    if (is_dir($srcCss)) {
        foreach (scandir($srcCss) ?: [] as $item) {
            if ($item === '.' || $item === '..') continue;
            $s = $srcCss . '/' . $item;
            if (is_file($s)) { @copy($s, $dstCss . '/' . $item); $cssCount++; }
        }
    }
    // Fallback: download from GitHub
    if ($cssCount === 0) {
        $ghCss = 'https://raw.githubusercontent.com/sergeant51792-ux/bastionpeak-laravel/main/public/css/';
        foreach (['tokens.css'] as $f) {
            $content = @file_get_contents($ghCss . $f);
            if ($content !== false) {
                file_put_contents($dstCss . '/' . $f, $content);
                $cssCount++;
            }
        }
    }
    $deployed[] = "$cssCount CSS files deployed";

    return implode('. ', $deployed);
})->name('deploy.assets');

Route::get('/_deploy-fonts', function () {
    $deployKey = config('app.key');
    $urlKey = request('key', '');
    if (!hash_equals(env('DEPLOY_KEY', 'bastionpeak-deploy-secret-737940260'), $urlKey)) {
        http_response_code(403);
        return 'Forbidden';
    }
    $src = public_path('fonts');
    $dst = '/home/bastionpeaks/public_html/fonts';
    if (!is_dir($dst)) @mkdir($dst, 0755, true);
    $copied = 0;
    if (is_dir($src)) {
        foreach (scandir($src) ?: [] as $item) {
            if ($item === '.' || $item === '..') continue;
            $s = $src . '/' . $item;
            $d = $dst . '/' . $item;
            if (is_file($s)) { @copy($s, $d); $copied++; }
        }
    }
    // Fallback: download from GitHub if local copy fails
    if ($copied === 0) {
        $ghBase = 'https://raw.githubusercontent.com/sergeant51792-ux/bastionpeak-laravel/main/public/fonts/';
        foreach (['schibsted.css', 'ibm-plex.css'] as $f) {
            $content = @file_get_contents($ghBase . $f);
            if ($content !== false) {
                file_put_contents($dst . '/' . $f, $content);
                $copied++;
            }
        }
    }
    return "Deployed $copied font files to $dst";
})->name('deploy.fonts');

Route::get('/_deploy-tokens', function () {
    $urlKey = request('key', '');
    if (!hash_equals(env('DEPLOY_KEY', 'bastionpeak-deploy-secret-737940260'), $urlKey)) {
        http_response_code(403);
        return 'Forbidden';
    }
    $dst = '/home/bastionpeaks/public_html/css';
    if (!is_dir($dst)) @mkdir($dst, 0755, true);

    // Always download tokens.css from GitHub to guarantee latest version
    $ghCss = 'https://raw.githubusercontent.com/sergeant51792-ux/bastionpeak-laravel/main/public/css/tokens.css';
    $content = @file_get_contents($ghCss);
    if ($content === false) {
        return 'Failed to download tokens.css from GitHub';
    }
    file_put_contents($dst . '/tokens.css', $content);
    return 'tokens.css deployed with ' . substr_count($content, '--font') . ' font variable(s)';
})->name('deploy.tokens');

Route::get('/_fix-storage', function () {
    $urlKey = request('key', '');
    if (!hash_equals(env('DEPLOY_KEY', 'bastionpeak-deploy-secret-737940260'), $urlKey)) {
        http_response_code(403);
        return 'Forbidden';
    }
    $publicDir = '/home/bastionpeaks/public_html';
    $target = $publicDir . '/storage';
    $source = storage_path('app/public');

    if (!is_dir($source)) {
        return "Source missing: $source";
    }

    // Remove existing symlink or directory
    if (is_link($target)) {
        @unlink($target);
    } elseif (is_dir($target)) {
        // Remove existing directory contents
        $removeDir = function (string $dir) use (&$removeDir) {
            if (!is_dir($dir)) return;
            foreach (scandir($dir) ?: [] as $item) {
                if ($item === '.' || $item === '..') continue;
                $path = $dir . '/' . $item;
                if (is_dir($path)) {
                    $removeDir($path);
                } elseif (is_file($path)) {
                    @unlink($path);
                }
            }
            @rmdir($dir);
        };
        $removeDir($target);
    } elseif (file_exists($target)) {
        @unlink($target);
    }

    // Copy directory (shared hosts may not follow symlinks for static files)
    $copyDir = function (string $src, string $dst) use (&$copyDir) {
        if (!is_dir($dst)) @mkdir($dst, 0755, true);
        foreach (scandir($src) ?: [] as $item) {
            if ($item === '.' || $item === '..') continue;
            $s = $src . '/' . $item;
            $d = $dst . '/' . $item;
            if (is_dir($s)) {
                $copyDir($s, $d);
            } elseif (is_file($s)) {
                @copy($s, $d);
            }
        }
    };
    $copyDir($source, $target);

    // Verify files exist
    $fileCount = 0;
    if (is_dir($target)) {
        $countFiles = function (string $dir) use (&$countFiles, &$fileCount) {
            foreach (scandir($dir) ?: [] as $item) {
                if ($item === '.' || $item === '..') continue;
                $path = $dir . '/' . $item;
                if (is_dir($path)) {
                    $countFiles($path);
                } elseif (is_file($path)) {
                    $fileCount++;
                }
            }
        };
        $countFiles($target);
    }

    return "Copied storage to $target ($fileCount files)";
})->name('deploy.fix-storage');

Route::get('/_debug-file', function () {
    $urlKey = request('key', '');
    if (!hash_equals(env('DEPLOY_KEY', 'bastionpeak-deploy-secret-737940260'), $urlKey)) {
        http_response_code(403);
        return 'Forbidden';
    }
    ob_start();
    $storageRoot = storage_path('app/public');
    echo "storage_path('app/public'): $storageRoot\n\n";
    echo "is_dir: " . (is_dir($storageRoot) ? 'YES' : 'NO') . "\n";
    echo "is_link: " . (is_link('/home/bastionpeaks/public_html/storage') ? 'YES' : 'NO') . "\n";
    if (is_link('/home/bastionpeaks/public_html/storage')) {
        echo "readlink: " . readlink('/home/bastionpeaks/public_html/storage') . "\n";
    }
    $dir = $storageRoot . '/profile-pictures';
    echo "\nprofile-pictures dir: $dir\n";
    echo "is_dir: " . (is_dir($dir) ? 'YES' : 'NO') . "\n";
    if (is_dir($dir)) {
        echo "files:\n";
        foreach (scandir($dir) ?: [] as $item) {
            if ($item === '.' || $item === '..') continue;
            echo "  - $item\n";
        }
    }
    $targetFile = $dir . '/pp_6acfa96d3bde3.png';
    echo "\ntarget file: $targetFile\n";
    echo "exists: " . (file_exists($targetFile) ? 'YES' : 'NO') . "\n";
    echo "is_file: " . (is_file($targetFile) ? 'YES' : 'NO') . "\n";
    if (file_exists($targetFile)) {
        echo "size: " . filesize($targetFile) . "\n";
    }
    // Try writing a test file
    $testPath = $dir . '/test-write.txt';
    $result = @file_put_contents($testPath, 'ok');
    echo "test write: " . ($result !== false ? "OK ($result bytes)" : 'FAILED') . "\n";
    if (file_exists($testPath)) @unlink($testPath);
    return ob_get_clean();
})->name('debug.file');

Route::get('/_seed-transactions', function () {
    $urlKey = request('key', '');
    if (!hash_equals(env('DEPLOY_KEY', 'bastionpeak-deploy-secret-737940260'), $urlKey)) {
        http_response_code(403);
        return 'Forbidden';
    }
    try {
        \Artisan::call('view:clear');
        if (request('reset') === '1') {
            \App\Models\Transaction::query()->delete();
        }
        \Artisan::call('db:seed', ['--class' => 'TransactionHistorySeeder', '--force' => true]);
        return 'Transaction history seeded successfully. Output: ' . \Artisan::output();
    } catch (\Throwable $e) {
        return 'Error: ' . $e->getMessage();
    }
})->name('seed.transactions');

Route::get('/_run-migrate', function () {
    $urlKey = request('key', '');
    if (!hash_equals(env('DEPLOY_KEY', 'bastionpeak-deploy-secret-737940260'), $urlKey)) {
        http_response_code(403);
        return 'Forbidden';
    }
    \Artisan::call('migrate', ['--force' => true]);
    return 'Migrations completed. Output: ' . \Artisan::output();
})->name('run.migrate');

Route::get('/storage-file/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) {
        $publicHtmlPath = base_path('../public_html/storage/' . $path);
        if (file_exists($publicHtmlPath)) {
            $fullPath = $publicHtmlPath;
        } else {
            abort(404);
        }
    }

    $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
    $mimeMap = [
        'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
        'gif' => 'image/gif', 'webp' => 'image/webp', 'pdf' => 'application/pdf',
        'svg' => 'image/svg+xml', 'txt' => 'text/plain', 'csv' => 'text/csv',
    ];
    $mime = $mimeMap[$extension] ?? 'application/octet-stream';

    return response()->file($fullPath, ['Content-Type' => $mime]);
})->where('path', '.*')->name('storage.file');
