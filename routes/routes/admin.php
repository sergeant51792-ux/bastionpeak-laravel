<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\MerchantController;
use App\Http\Controllers\Admin\CreditDebitController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\FinancialServiceController;

Route::middleware(['web', 'auth', 'role:Super Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('overview');
    Route::get('approvals', ApprovalController::class)->name('approvals');
    Route::post('approvals/batch', [ApprovalController::class, 'batchApprove'])->name('approvals.batch');
    Route::post('approvals/batch-delete', [ApprovalController::class, 'batchDelete'])->name('approvals.batch-delete');
    Route::post('approvals/{transaction}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('approvals/{transaction}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::match(['POST', 'PATCH'], 'users/{user}', [UserController::class, 'updateUser'])->name('users.update');
    Route::post('users/{user}/password', [UserController::class, 'changePassword'])->name('users.password');
    Route::post('users/{user}/message', [UserController::class, 'sendMessage'])->name('users.message');
    Route::post('users/{user}/credit', [UserController::class, 'credit'])->name('users.credit');
    Route::post('users/{user}/debit', [UserController::class, 'debit'])->name('users.debit');
    Route::get('users/{user}/accounts', [UserController::class, 'accounts'])->name('users.accounts');
    Route::get('users/{user}/transactions', [UserController::class, 'transactions'])->name('users.transactions');
    Route::post('users/{user}/block', [UserController::class, 'block'])->name('users.block');
    Route::post('users/{user}/freeze', [UserController::class, 'freeze'])->name('users.freeze');

    Route::get('accounts/{account}/deposit-methods', [\App\Http\Controllers\Admin\AccountDepositMethodController::class, 'index'])->name('accounts.deposit-methods.index');
    Route::post('accounts/{account}/deposit-methods', [\App\Http\Controllers\Admin\AccountDepositMethodController::class, 'store'])->name('accounts.deposit-methods.store');
    Route::put('accounts/{account}/deposit-methods/{depositMethod}', [\App\Http\Controllers\Admin\AccountDepositMethodController::class, 'update'])->name('accounts.deposit-methods.update');
    Route::delete('accounts/{account}/deposit-methods/{depositMethod}', [\App\Http\Controllers\Admin\AccountDepositMethodController::class, 'destroy'])->name('accounts.deposit-methods.destroy');

    Route::get('deposit-methods', [\App\Http\Controllers\Admin\DepositMethodController::class, 'index'])->name('deposit-methods.index');
    Route::get('deposit-methods/create', [\App\Http\Controllers\Admin\DepositMethodController::class, 'create'])->name('deposit-methods.create');
    Route::post('deposit-methods', [\App\Http\Controllers\Admin\DepositMethodController::class, 'store'])->name('deposit-methods.store');
    Route::get('deposit-methods/{depositMethod}/edit', [\App\Http\Controllers\Admin\DepositMethodController::class, 'edit'])->name('deposit-methods.edit');
    Route::put('deposit-methods/{depositMethod}', [\App\Http\Controllers\Admin\DepositMethodController::class, 'update'])->name('deposit-methods.update');
    Route::delete('deposit-methods/{depositMethod}', [\App\Http\Controllers\Admin\DepositMethodController::class, 'destroy'])->name('deposit-methods.destroy');

    Route::get('accounts/{account}', [AccountController::class, 'show'])->name('accounts.show');
    Route::post('accounts/{account}/balance', [AccountController::class, 'updateBalance'])->name('accounts.balance.update');
    Route::post('accounts/{account}/status', [AccountController::class, 'updateStatus'])->name('accounts.status.update');
    Route::post('accounts/{account}/caps', [AccountController::class, 'updateCaps'])->name('accounts.caps.update');
    Route::post('accounts/{account}/deposit', [AccountController::class, 'updateDepositDetails'])->name('accounts.deposit.update');
    Route::post('accounts/{account}/recalculate', [AccountController::class, 'recalculate'])->name('accounts.recalculate');

    Route::get('card-requests', [\App\Http\Controllers\Admin\CardRequestController::class, 'index'])->name('card-requests.index');
    Route::post('card-requests/{cardRequest}/approve', [\App\Http\Controllers\Admin\CardRequestController::class, 'approve'])->name('card-requests.approve');
    Route::post('card-requests/{cardRequest}/reject', [\App\Http\Controllers\Admin\CardRequestController::class, 'reject'])->name('card-requests.reject');

    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('transactions/create/{user?}', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::match(['POST', 'PATCH', 'PUT'], 'transactions/{transaction}', [UserController::class, 'updateTransaction'])->name('transactions.update');
    Route::delete('transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    Route::post('transactions/{transaction}/reverse', [TransactionController::class, 'reverse'])->name('transactions.reverse');
    Route::post('transactions/export', [TransactionController::class, 'export'])->name('transactions.export');

    Route::get('merchants', [MerchantController::class, 'index'])->name('merchants.index');
    Route::get('merchants/{merchant}', [MerchantController::class, 'show'])->name('merchants.show');
    Route::post('merchants/{merchant}/deactivate', [MerchantController::class, 'deactivate'])->name('merchants.deactivate');

    Route::post('cards/bulk', [\App\Http\Controllers\Admin\CardController::class, 'bulkAction'])->name('cards.bulk');
    Route::get('cards', \App\Http\Controllers\Admin\CardController::class)->name('cards.index');
    Route::get('cards/{card}', [\App\Http\Controllers\Admin\CardController::class, 'show'])->name('cards.show');
    Route::post('cards/{card}/freeze', [\App\Http\Controllers\Admin\CardController::class, 'freeze'])->name('cards.freeze');
    Route::post('cards/{card}/unfreeze', [\App\Http\Controllers\Admin\CardController::class, 'unfreeze'])->name('cards.unfreeze');
    Route::post('cards/{card}/block', [\App\Http\Controllers\Admin\CardController::class, 'block'])->name('cards.block');
    Route::post('cards/{card}/cancel', [\App\Http\Controllers\Admin\CardController::class, 'cancel'])->name('cards.cancel');

    Route::get('messages', \App\Http\Controllers\Admin\MessageController::class)->name('messages.index');
    Route::get('messages/thread/{threadId}', [\App\Http\Controllers\Admin\MessageController::class, 'showThread'])->name('messages.thread');
    Route::post('messages/reply', [\App\Http\Controllers\Admin\MessageController::class, 'reply'])->name('messages.reply');
    Route::post('messages/broadcast', [\App\Http\Controllers\Admin\MessageController::class, 'broadcast'])->name('messages.broadcast');
    Route::delete('messages/thread/{message}', [\App\Http\Controllers\Admin\MessageController::class, 'destroyThread'])->name('messages.thread.destroy');

    Route::get('reports', \App\Http\Controllers\Admin\ReportController::class)->name('reports.index');
    Route::get('reports/export', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export');

    Route::get('audit-log', \App\Http\Controllers\Admin\AuditLogController::class)->name('audit-log.index');

    Route::get('settings', \App\Http\Controllers\Admin\SettingController::class)->name('settings.index');
    Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

    Route::get('financial-services', [FinancialServiceController::class, 'index'])->name('financial-services.index');
    Route::get('financial-services/{service}/applications', [FinancialServiceController::class, 'applications'])->name('financial-services.applications');
    Route::post('financial-services/applications/{application}/approve', [FinancialServiceController::class, 'approveApplication'])->name('financial-services.applications.approve');
    Route::post('financial-services/applications/{application}/reject', [FinancialServiceController::class, 'rejectApplication'])->name('financial-services.applications.reject');
});
