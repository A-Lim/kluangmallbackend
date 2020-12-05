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
            Route::get('landing', 'LandingController@details');
            Route::patch('landing', 'LandingController@update');
        });

        /**** Merchants ****/
        Route::namespace('API\v1\Merchant')->group(function () {
            Route::get('merchants', 'MerchantController@list');
            Route::get('merchants/categories', 'MerchantController@listCategories');
            Route::get('merchants/{merchant}', 'MerchantController@details');
            Route::post('merchants', 'MerchantController@create');
            Route::post('merchants/track', 'MerchantController@track');
            Route::patch('merchants/{merchant}', 'MerchantController@update');
            Route::delete('merchants/{merchant}', 'MerchantController@delete');
            Route::delete('merchants/categories/{merchantCategory}', 'MerchantController@deleteMerchantCategory');

            Route::get('merchants/{merchant}/users', 'MerchantController@listUsers');
            Route::post('merchants/{merchant}/users', 'MerchantController@createUsers');
        });

        /**** Page ****/
        Route::namespace('API\v1\Page')->group(function () {
            Route::get('page/landing', 'PageController@landing');
            Route::get('page/about-us', 'PageController@aboutUs');
            Route::get('page/contact-us', 'PageController@contactUs');
            Route::get('page/privacy-policy', 'PageController@privacyPolicy');
            Route::get('page/terms-and-conditions', 'PageController@termsAndConditions');
            Route::get('page/shops', 'PageController@shops');
            Route::get('page/shops/{merchant}', 'PageController@shopDetail');
        });

    });
});
