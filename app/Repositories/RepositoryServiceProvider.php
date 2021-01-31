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

        $this->app->bind('App\Repositories\Feedback\IFeedbackRepository', 'App\Repositories\Feedback\FeedbackRepository');
        $this->app->bind('App\Repositories\Event\IEventRepository', 'App\Repositories\Event\EventRepository');
        $this->app->bind('App\Repositories\Promotion\IPromotionRepository', 'App\Repositories\Promotion\PromotionRepository');
        $this->app->bind('App\Repositories\Banner\IBannerRepository', 'App\Repositories\Banner\BannerRepository');
        $this->app->bind('App\Repositories\Landing\ILandingRepository', 'App\Repositories\Landing\LandingRepository');
        $this->app->bind('App\Repositories\Merchant\IMerchantRepository', 'App\Repositories\Merchant\MerchantRepository');
        $this->app->bind('App\Repositories\Merchant\IMerchantAccountRepository', 'App\Repositories\Merchant\MerchantAccountRepository');
        $this->app->bind('App\Repositories\Notification\INotificationRepository', 'App\Repositories\Notification\NotificationRepository');
        $this->app->bind('App\Repositories\Announcement\IAnnouncementRepository', 'App\Repositories\Announcement\AnnouncementRepository');
        $this->app->bind('App\Repositories\Voucher\IVoucherRepository', 'App\Repositories\Voucher\VoucherRepository');
        $this->app->bind('App\Repositories\Voucher\IVoucherTransactionRepository', 'App\Repositories\Voucher\VoucherTransactionRepository');
        $this->app->bind('App\Repositories\Dashboard\IDashboardRepository', 'App\Repositories\Dashboard\DashboardRepository');
        $this->app->bind('App\Repositories\Receipt\IReceiptRepository', 'App\Repositories\Receipt\ReceiptRepository');
    }
}