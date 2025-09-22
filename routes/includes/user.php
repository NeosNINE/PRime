<?php

use App\Http\Controllers\User\UserExtraController;
use App\Http\Controllers\User\UserProfileController;
use App\Http\Controllers\User\UserIndexController;
use App\Http\Controllers\User\UserBalanceController;

Route::prefix('user')->name('user.')->group(function(){

    Route::get('profile', UserProfileController::class)->name('profile');
    Route::post('profile/email', [UserProfileController::class, 'updateEmail'])->name('profile.email');
    Route::post('profile/avatar', [UserProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('profile/password', [UserProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('referral', [UserIndexController::class, 'referralPage'])->name('referral');

    Route::get('orders', [UserIndexController::class, 'ordersPage'])->name('orders');

    Route::get('orders-history', [UserIndexController::class, 'ordersHistoryPage'])->name('orders-history');

    Route::get('services', [UserIndexController::class, 'servicesPage'])->name('services');

    Route::get('balance-topup', [UserIndexController::class, 'balanceTopupPage'])->name('balance-topup');
    Route::post('balance/fake-topup', [UserBalanceController::class, 'fakeTopup'])->name('balance.fake_topup');
    Route::get('balance/transactions', [UserBalanceController::class, 'listTransactions'])->name('balance.transactions');
    Route::post('promo/apply', [UserBalanceController::class, 'applyPromo'])->name('promo.apply');

    Route::get('tickets', [UserIndexController::class, 'ticketsPage'])->name('tickets');

    Route::get('updates', [UserIndexController::class, 'updatesPage'])->name('updates');

    Route::get('api', [UserIndexController::class, 'apiPage'])->name('api');

    Route::get('notifications', [UserIndexController::class, 'notificationsPage'])->name('notifications');

    Route::get('refresh-data', [UserExtraController::class, 'refreshData'])->name('refresh_data');

    // Notifications
    Route::post('notifications/read', [UserExtraController::class, 'markNotificationRead'])->name('notifications.read');
    Route::post('notifications/delete', [UserExtraController::class, 'deleteNotification'])->name('notifications.delete');
    Route::post('notifications/read-all', [UserExtraController::class, 'markAllNotificationsRead'])->name('notifications.read_all');
    Route::get('notifications/list', [UserExtraController::class, 'notificationsList'])->name('notifications.list');
    Route::post('notifications/delete-all', [UserExtraController::class, 'deleteAllNotifications'])->name('notifications.delete_all');

    // 2FA
    Route::get('profile/2fa/status', [UserProfileController::class, 'twoFactorStatus'])->name('profile.2fa.status');
    Route::post('profile/2fa/setup', [UserProfileController::class, 'twoFactorSetup'])->name('profile.2fa.setup');
    Route::post('profile/2fa/enable', [UserProfileController::class, 'twoFactorEnable'])->name('profile.2fa.enable');
    Route::post('profile/2fa/disable', [UserProfileController::class, 'twoFactorDisable'])->name('profile.2fa.disable');

});
