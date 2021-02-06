<?php

Route::prefix('v1')->group(function () {

    /**** Auth ****/
    Route::namespace('API\v1\Auth')->group(function () {
        Route::post('login', 'LoginController@login');
        Route::post('logout', 'LoginController@logout');

        // Route::post('token/refresh', 'LoginController@refresh');
        Route::post('register', 'RegistrationController@register');

        Route::post('forgot-password', 'ForgotPasswordController@sendResetLink');
        Route::post('forgot-password/otp', 'ForgotPasswordController@sendForgetPasswordOTP');

        Route::post('reset-password', 'ForgotPasswordController@resetPassword');
        Route::post('reset-password/otp', 'ForgotPasswordController@resetPasswordOTP');


        Route::get('verify-email', 'VerificationController@verifyEmail')->name('verification.verify');
        Route::post('verify-email', 'VerificationController@sendVerificationEmail');

        Route::post('verify-otp', 'VerificationController@verifyOTP');
    });

    /**** Api Logs ****/
    Route::namespace('API\v1\ApiLog')->group(function () {
        Route::get('api-logs', 'ApiLogController@list');
    });

    Route::middleware(['apilogger'])->group(function () {
        /**** User ****/
        Route::namespace('API\v1\User')->group(function () {
            Route::get('users', 'UserController@list');
            Route::get('users/{user}', 'UserController@details');
            Route::get('profile', 'UserController@profile');
            Route::post('users/{user}/reset-password', 'UserController@resetPassword');

            Route::patch('profile', 'UserController@updateProfile');
            Route::patch('change-password', 'UserController@changePassword');

            Route::patch('users/{user}', 'UserController@update');

            Route::patch('users/{user}/avatar', 'UserController@uploadUserAvatar');
            Route::patch('profile/avatar', 'UserController@uploadProfileAvatar');
        });

        /**** UserGroups ****/
        Route::namespace('API\v1\UserGroup')->group(function () {

            Route::get('usergroups', 'UserGroupController@list');
            Route::get('usergroups/{userGroup}', 'UserGroupController@details');
            Route::get('usergroups/{userGroup}/users', 'UserGroupController@listUsers');
            Route::get('usergroups/{userGroup}/notusers', 'UserGroupController@listNotUsers');
            
            Route::post('usergroups', 'UserGroupController@create');
            Route::post('usergroups/exists', 'UserGroupController@exists');
            Route::post('usergroups/{userGroup}/users', 'UserGroupController@addUsers');

            Route::patch('usergroups/{userGroup}', 'UserGroupController@update');
            Route::delete('usergroups/{userGroup}', 'UserGroupController@delete');
            Route::delete('usergroups/{userGroup}/users/{user}', 'UserGroupController@removeUser');
        });

        /**** SystemSettings ****/
        Route::namespace('API\v1\SystemSetting')->group(function () {
            Route::post('app/version', 'SystemSettingController@appVersion');

            Route::get('systemsettings', 'SystemSettingController@list');
            Route::get('systemsettings/allowpublicregistration', 'SystemSettingController@allowPublicRegistration');
            Route::patch('systemsettings', 'SystemSettingController@update');
        });

        /**** Permissions ****/
        Route::namespace('API\v1\Permission')->group(function () {
            Route::get('permissions', 'PermissionController@list');
        });

        /**** Events ****/
        Route::namespace('API\v1\Feedback')->group(function () {
            Route::get('feedbacks', 'FeedbackController@list');
            Route::post('feedbacks', 'FeedbackController@create');
            Route::delete('feedbacks/{feedback}', 'FeedbackController@delete');
        });

        /**** Events ****/
        Route::namespace('API\v1\Event')->group(function () {
            Route::get('events', 'EventController@list');
            Route::get('events/{event}', 'EventController@details');
            Route::post('events', 'EventController@create');
            Route::patch('events/{event}', 'EventController@update');
            Route::delete('events/{event}', 'EventController@delete');
        });

        /**** Promotions ****/
        Route::namespace('API\v1\Promotion')->group(function () {
            Route::get('promotions', 'PromotionController@list');
            Route::get('promotions/{promotion}', 'PromotionController@details');
            Route::post('promotions', 'PromotionController@create');
            Route::patch('promotions/{promotion}', 'PromotionController@update');
            Route::delete('promotions/{promotion}', 'PromotionController@delete');
        });

        /**** Banners ****/
        Route::namespace('API\v1\Banner')->group(function () {
            Route::get('banners', 'BannerController@list');
            Route::get('banners/{banner}', 'BannerController@details');
            Route::post('banners', 'BannerController@create');
            Route::patch('banners/{banner}', 'BannerController@update');
            Route::delete('banners/{banner}', 'BannerController@delete');
        });

        /**** Landing ****/
        Route::namespace('API\v1\Landing')->group(function () {
            Route::get('landing/{app}', 'LandingController@details');
            Route::patch('landing', 'LandingController@update');
        });

        /**** Merchants ****/
        Route::namespace('API\v1\Merchant')->group(function () {
            Route::get('merchants', 'MerchantController@list');
            Route::get('merchants/profile', 'MerchantController@profile');
            Route::get('merchants/categories', 'MerchantController@listCategories');
            Route::get('merchants/{merchant}', 'MerchantController@details');
            Route::post('merchants', 'MerchantController@create');
            Route::post('merchants/track', 'MerchantController@track');
            Route::patch('merchants/{merchant}', 'MerchantController@update');
            Route::delete('merchants/{merchant}', 'MerchantController@delete');
            Route::delete('merchants/categories/{merchantCategory}', 'MerchantController@deleteMerchantCategory');

            Route::get('merchants/{merchant}/users', 'MerchantController@listUsers');
            Route::post('merchants/{merchant}/users', 'MerchantController@createUsers');

            // credits 
            Route::get('merchants/my/transactions', 'MerchantAccountController@listMyTransactions');
            Route::post('merchants/my/topup', 'MerchantAccountController@topUpMyAccount');


            Route::get('merchants/{merchant}/transactions', 'MerchantAccountController@listTransactions');
            Route::post('merchants/{merchant}/topup', 'MerchantAccountController@topUp');
            Route::post('merchants/refund/{transaction}', 'MerchantAccountController@refund');
        });

        /**** Page ****/
        Route::namespace('API\v1\Page')->group(function () {
            Route::get('page/landing/user', 'PageController@userLanding');
            Route::get('page/landing/merchant', 'PageController@merchantLanding');

            Route::get('page/about-us', 'PageController@aboutUs');
            Route::get('page/contact-us', 'PageController@contactUs');
            Route::get('page/privacy-policy', 'PageController@privacyPolicy');
            Route::get('page/terms-and-conditions', 'PageController@termsAndConditions');
            Route::get('page/shops', 'PageController@shops');
            Route::get('page/shops/{merchant}', 'PageController@shopDetail');
        });

        /**** Notifications ****/
        Route::namespace('API\v1\Notification')->group(function () {
            Route::get('notifications', 'NotificationController@list');
            Route::post('notifications/{notification}/read', 'NotificationController@read');
            Route::post('notifications/all', 'NotificationController@readAll');
            Route::delete('notifications/{notification}', 'NotificationController@delete');
        });

        /**** Announcements ****/
        Route::namespace('API\v1\Announcement')->group(function () {
            Route::get('announcements', 'AnnouncementController@list');
            Route::get('announcements/pending', 'AnnouncementController@pendingCount');
            Route::get('announcements/{announcement}', 'AnnouncementController@details');
            Route::post('announcements', 'AnnouncementController@create');
            Route::patch('announcements/{announcement}', 'AnnouncementController@update');
            Route::post('announcements/{announcement}/approve', 'AnnouncementController@approve');
            Route::post('announcements/{announcement}/reject', 'AnnouncementController@reject');
        });

        /**** Vouchers ****/
        Route::namespace('API\v1\Voucher')->group(function () {
            Route::get('vouchers/transactions', 'VoucherTransactionController@list');
        
            Route::get('vouchers', 'VoucherController@list');
            Route::get('vouchers/merchant/active', 'VoucherController@listMerchantsActive');
            Route::get('vouchers/merchant/inactive', 'VoucherController@listMerchantsInactive');
            Route::get('vouchers/{voucher}', 'VoucherController@details');
            Route::post('vouchers', 'VoucherController@create');
            Route::post('vouchers/{voucher}/redeem', 'VoucherController@redeem');
            Route::patch('vouchers/{voucher}', 'VoucherController@update');
            Route::delete('vouchers/{voucher}', 'VoucherController@delete');

            // MyVouchers
            Route::get('vouchers/my/transactions', 'VoucherTransactionController@listMy');
            Route::get('vouchers/my/active', 'MyVoucherController@listMyActive');
            Route::get('vouchers/my/inactive', 'MyVoucherController@listMyInactive');
            Route::get('vouchers/my/{myVoucher}', 'MyVoucherController@details');
            Route::post('vouchers/my/{myVoucher}/swipetouse', 'MyVoucherController@swipetouse');
            Route::post('vouchers/my/{myVoucher}/userscanmerchant', 'MyVoucherController@userscanmerchant');
            Route::post('vouchers/my/{myVoucher}/merchantscanuser', 'MyVoucherController@merchantscanuser');
        });

        /**** Rewards ****/
        Route::namespace('API\v1\Voucher')->group(function () {
            Route::get('rewards', 'VoucherController@listRewards');
            Route::get('rewards/{voucher}', 'VoucherController@rewardDetails');
        });

        /**** Dashboard ****/
        Route::namespace('API\v1\Dashboard')->group(function () {
            Route::get('dashboard/stats', 'DashboardController@stats');
            Route::get('dashboard/top10merchantvisits', 'DashboardController@top_10_merchant_visits');
            Route::get('dashboard/newusers', 'DashboardController@new_users');
        });

        /**** Receipts ****/
        Route::namespace('API\v1\Receipt')->group(function () {
            Route::get('receipts/my', 'ReceiptController@listMy');
            Route::post('receipts/upload', 'ReceiptController@upload');
        });

        /**** Point ****/
        Route::namespace('API\v1\Point')->group(function () {
            Route::get('points', 'PointController@list');
            Route::get('points/my', 'PointController@listMy');
        });
    });
});
