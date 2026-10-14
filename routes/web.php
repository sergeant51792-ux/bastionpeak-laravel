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
        Route::get('/transfer', [TransferController::class, '__invoke'])->name('transfer');
        Route::get('/top-up', [TopUpController::class, '__invoke'])->name('top-up');
        Route::post('/top-up', [TopUpController::class, 'submit'])->name('top-up.submit');
        Route::get('/pay', [PayController::class, '__invoke'])->name('pay');
        Route::post('/pay', [PayController::class, 'submit'])->name('pay.submit');
        Route::get('/accounts/{account}', [AccountController::class, 'show'])->name('accounts.show');
        Route::get('/receive', [ReceiveController::class, '__invoke'])->name('receive');
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
