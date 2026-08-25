<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Auth')->middleware('vendor.guest')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('logout', 'logout')->middleware('vendor')->withoutMiddleware('vendor.guest')->name('logout');
    });

    Route::controller('RegisterController')->group(function () {
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register');
        Route::post('check-user', 'checkUser')->name('checkUser')->withoutMiddleware('vendor.guest');
    });

    Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
        Route::get('reset', 'showLinkRequestForm')->name('request');
        Route::post('email', 'sendResetCodeEmail')->name('email');
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code');
    });

    Route::controller('ResetPasswordController')->group(function () {
        Route::post('password/reset', 'reset')->name('password.update');
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
    });

    Route::controller('SocialiteController')->group(function () {
        Route::get('social-login/{provider}', 'socialLogin')->name('social.login');
        Route::get('social-login/callback/{provider}', 'callback')->name('social.login.callback');
    });
});

Route::middleware('vendor')->group(function () {

    // User Support Ticket
    Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
        Route::get('/', 'supportTicket')->name('index');
        Route::get('new', 'openSupportTicket')->name('open');
        Route::post('create', 'storeSupportTicket')->name('store');
        Route::get('view/{ticket}', 'viewTicket')->name('view');
        Route::post('reply/{id}', 'replyTicket')->name('reply');
        Route::post('close/{id}', 'closeTicket')->name('close');
        Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
    });

    Route::get('user-data', 'VendorController@userData')->name('data');
    Route::post('user-data-submit', 'VendorController@userDataSubmit')->name('data.submit');

    //authorization
    Route::middleware('vendor.registration.complete')->controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'emailVerification')->name('verify.email');
        Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('verify-g2fa', 'g2faVerification')->name('2fa.verify');
    });

    Route::middleware(['vendor.check.status', 'vendor.registration.complete'])->group(function () {

        Route::controller('VendorController')->group(function () {
            Route::get('dashboard', 'home')->name('home');
            Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');

            //2FA
            Route::get('twofactor', 'show2faForm')->name('twofactor');
            Route::post('twofactor/enable', 'create2fa')->name('twofactor.enable');
            Route::post('twofactor/disable', 'disable2fa')->name('twofactor.disable');

            //KYC
            Route::get('kyc-form', 'kycForm')->name('kyc.form');
            Route::get('kyc-data', 'kycData')->name('kyc.data');
            Route::post('kyc-submit', 'kycSubmit')->name('kyc.submit');

            //Report
            Route::any('chart/sales', 'salesChart')->name('chart.sales');
            Route::any('deposit/history', 'depositHistory')->name('deposit.history');
            Route::get('transactions', 'transactions')->name('transactions');

            Route::post('add-device-token', 'addDeviceToken')->name('add.device.token');

            Route::get('delivery', 'delivery')->name('delivery');
            Route::post('delivery', 'updateDelivery')->name('delivery');

            Route::get('design/instruction', 'designInstruction')->name('design.instruction');
            Route::post('design/instruction', 'updateDesignInstruction')->name('design.instruction');
        });

        //Profile setting
        Route::controller('ProfileController')->group(function () {
            Route::get('profile-setting', 'profile')->name('profile.setting');
            Route::post('profile-setting', 'submitProfile');
            Route::get('change-password', 'changePassword')->name('change.password');
            Route::post('change-password', 'submitPassword');
        });

        //Notification
        Route::controller('NotificationController')->prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', 'notifications')->name('index');
            Route::get('read/{id}', 'notificationRead')->name('read');
            Route::get('read-all', 'readAllNotification')->name('read.all');
            Route::post('delete-single/{id}', 'deleteSingleNotification')->name('delete.single');
            Route::post('delete-all', 'deleteAllNotification')->name('delete.all');
        });

        Route::prefix('product')->name('product.')->group(function () {
            //Product Attributes
            Route::controller('AttributeController')->prefix('attribute')->name('attribute.')->group(function () {
                Route::get('', 'index')->name('index');
                Route::post('store/{id?}', 'store')->name('store');
                Route::post('update-status/{id}', 'status')->name('status');
                Route::get('download', 'downloadAttributes')->name('download');
            });

            //Product Attribute Values
            Route::controller('AttributeValueController')->prefix('attribute/values')->name('attribute.values.')->group(function () {
                Route::get('{id}/all', 'index')->name('index');
                Route::post('save/{id}', 'store')->name('store');
            });

        });
        Route::controller('ProductController')->group(function () {
            Route::prefix('products')->name('products.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('create/{slug?}/{step?}', 'create')->name('create');
                Route::post('store/{id?}', 'store')->name('store');
                Route::get('edit/{slug}/{step?}', 'edit')->name('edit');
                Route::post('image/delete/{id}', 'deleteImage')->name('image.delete');
                Route::get('generate/variants/{id}', 'generateVariants')->name('generate.variants');
                Route::post('status/{id}', 'status')->name('status');
            });
            Route::get('reviews', 'reviews')->name('reviews');
        });

        // order controller
        Route::controller('OrderController')->prefix('order')->name('order.')->group(function () {
            Route::get('/', 'allOrders')->name('index');
            Route::get('pending', 'pendingOrders')->name('pending');
            Route::get('processing', 'processingOrders')->name('processing');
            Route::get('dispatched', 'dispatchedOrders')->name('dispatched');
            Route::get('completed', 'completedOrders')->name('completed');
            Route::get('canceled', 'canceledOrders')->name('canceled');
            Route::get('details/{order_number}', 'orderDetails')->name('details');
            Route::post('change/status/{id}', 'changeStatus')->name('status.change');
            Route::post('cancel/status/{id}', 'cancelStatus')->name('status.cancel');
            Route::get('print/area/{orderNumber}/{id}', 'orderPrintArea')->name('print.area');
            Route::get('print/invoice/{orderNumber}', 'printInvoice')->name('print.invoice');
        });

        // Withdraw
        Route::controller('WithdrawController')->prefix('withdraw')->name('withdraw')->group(function () {
            Route::middleware('vendor.kyc')->group(function () {
                Route::get('/', 'withdrawMoney');
                Route::post('/', 'withdrawStore')->name('.money');
                Route::get('preview', 'withdrawPreview')->name('.preview');
                Route::post('preview', 'withdrawSubmit')->name('.submit');
            });
            Route::get('history', 'withdrawLog')->name('.history');
        });

    });
});
