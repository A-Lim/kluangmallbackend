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
            Route::patch('users/{user}', 'UserController@update');

            Route::patch('users/{user}/avatar', 'UserController@uploadUserAvatar');
            Route::patch('profile/avatar', 'UserController@uploadProfileAvatar');
        });

        /**** UserGroups ****/
        Route::namespace('API\v1\UserGroup')->group(function () {
            
            Route::get('usergroups', 'UserGroupController@list');
            Route::get('usergroups/{userGroup}', 'UserGroupController@details');
            Route::post('usergroups', 'UserGroupController@create');
            Route::post('usergroups/exists', 'UserGroupController@exists');
            Route::patch('usergroups/{userGroup}', 'UserGroupController@update');
            Route::delete('usergroups/{userGroup}', 'UserGroupController@delete');
        });

        /**** SystemSettings ****/
        Route::namespace('API\v1\SystemSetting')->group(function () {
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
            Route::post('banners/{banner}/activate', 'BannerController@activate');
            Route::post('banners/{banner}/deactivate', 'BannerController@deactivate');
            // Route::patch('banners/{banner}', 'PromotionController@update');
            Route::delete('banners/{banner}', 'BannerController@delete');
        });

        /**** Landing ****/
        Route::namespace('API\v1\Landing')->group(function () {
            Route::get('landing', 'LandingController@details');
        });

        /**** Page ****/
        Route::namespace('API\v1\Page')->group(function () {
            Route::get('page/landing', 'PageController@landing');
        });

    });
});
