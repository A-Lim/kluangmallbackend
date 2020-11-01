<?php
namespace App\Repositories;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider {

    public function register() {
        $this->app->bind('App\Repositories\Auth\IOAuthRepository', 'App\Repositories\Auth\OAuthRepository');
        $this->app->bind('App\Repositories\User\IUserRepository', 'App\Repositories\User\UserRepository');
        $this->app->bind('App\Repositories\UserGroup\IUserGroupRepository', 'App\Repositories\UserGroup\UserGroupRepository');
        $this->app->bind('App\Repositories\SystemSetting\ISystemSettingRepository', 'App\Repositories\SystemSetting\SystemSettingRepository');
        $this->app->bind('App\Repositories\Permission\IPermissionRepository', 'App\Repositories\Permission\PermissionRepository');
        $this->app->bind('App\Repositories\ApiLog\IApiLogRepository', 'App\Repositories\ApiLog\ApiLogRepository');
    }
}