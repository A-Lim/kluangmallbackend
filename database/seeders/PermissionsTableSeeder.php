<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Permission;
use App\PermissionModule;
use Carbon\Carbon;

class PermissionsTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $now = Carbon::now()->toDateTimeString();
        $permission_modules = [
            ['code' => 'users', 'name' => 'Users', 'description' => 'User module', 'is_active' => '1'],
            ['code' => 'usergroups', 'name' => 'User Groups', 'description' => 'User Groups module', 'is_active' => '1'],
            ['code' => 'systemsettings', 'name' => 'System Settings', 'description' => 'System Settings module', 'is_active' => '1'],
            ['code' => 'landing', 'name' => 'Landing', 'description' => 'Landing module', 'is_active' => '1'],
            ['code' => 'banners', 'name' => 'Banners', 'description' => 'Banners module', 'is_active' => '1'],
            ['code' => 'events', 'name' => 'Events', 'description' => 'Events module', 'is_active' => '1'],
            ['code' => 'promotions', 'name' => 'Promotions', 'description' => 'Promotions module', 'is_active' => '1'],
            ['code' => 'merchants', 'name' => 'Merchants', 'description' => 'Merchants module', 'is_active' => '1'],
            ['code' => 'announcements', 'name' => 'Announcements', 'description' => 'Announcements module', 'is_active' => '1'],
            ['code' => 'vouchers', 'name' => 'Vouchers', 'description' => 'Vouchers module', 'is_active' => '1'],
            ['code' => 'feedbacks', 'name' => 'Feedbacks', 'description' => 'Feedbacks module', 'is_active' => '1'],
            ['code' => 'apilogs', 'name' => 'Api Logs', 'description' => 'Api Logs module', 'is_active' => '1'],
        ];

        $permissions = [
            // users
            ['permission_module_id' => '1', 'code' => 'users.view', 'name' => 'View User', 'description' => ''],
            ['permission_module_id' => '1', 'code' => 'users.viewAny', 'name' => 'View Any Users', 'description' => ''],
            ['permission_module_id' => '1', 'code' => 'users.create', 'name' => 'Create Users', 'description' => ''],
            ['permission_module_id' => '1', 'code' => 'users.update', 'name' => 'Update Users', 'description' => ''],
            ['permission_module_id' => '1', 'code' => 'users.delete', 'name' => 'Delete Users', 'description' => ''],
            // usergroups
            ['permission_module_id' => '2', 'code' => 'usergroups.view', 'name' => 'View User Group', 'description' => ''],
            ['permission_module_id' => '2', 'code' => 'usergroups.viewAny', 'name' => 'View Any User Groups', 'description' => ''],
            ['permission_module_id' => '2', 'code' => 'usergroups.create', 'name' => 'Create User Groups', 'description' => ''],
            ['permission_module_id' => '2', 'code' => 'usergroups.update', 'name' => 'Update User Groups', 'description' => ''],
            ['permission_module_id' => '2', 'code' => 'usergroups.delete', 'name' => 'Delete User Groups', 'description' => ''],
            // systemsettings
            ['permission_module_id' => '3', 'code' => 'systemsettings.viewAny', 'name' => 'View Any System Settings', 'description' => ''],
            ['permission_module_id' => '3', 'code' => 'systemsettings.update', 'name' => 'Update System Settings', 'description' => ''],
            // landing
            ['permission_module_id' => '4', 'code' => 'landing.viewAny', 'name' => 'View Landing Management Page', 'description' => ''],
            ['permission_module_id' => '4', 'code' => 'landing.update', 'name' => 'Update Landing Page', 'description' => ''],
            // banners
            ['permission_module_id' => '5', 'code' => 'banners.view', 'name' => 'View Banner', 'description' => ''],
            ['permission_module_id' => '5', 'code' => 'banners.viewAny', 'name' => 'View Any Banners', 'description' => ''],
            ['permission_module_id' => '5', 'code' => 'banners.create', 'name' => 'Create Banners', 'description' => ''],
            ['permission_module_id' => '5', 'code' => 'banners.delete', 'name' => 'Delete Banners', 'description' => ''],
            ['permission_module_id' => '5', 'code' => 'banners.update', 'name' => 'Update Banners', 'description' => ''],
            // events
            ['permission_module_id' => '6', 'code' => 'events.view', 'name' => 'View Event', 'description' => ''],
            ['permission_module_id' => '6', 'code' => 'events.viewAny', 'name' => 'View Any Events', 'description' => ''],
            ['permission_module_id' => '6', 'code' => 'events.create', 'name' => 'Create Events', 'description' => ''],
            ['permission_module_id' => '6', 'code' => 'events.delete', 'name' => 'Delete Events', 'description' => ''],
            ['permission_module_id' => '6', 'code' => 'events.update', 'name' => 'Update Events', 'description' => ''],
            // promotions
            ['permission_module_id' => '7', 'code' => 'promotions.view', 'name' => 'View Promotion', 'description' => ''],
            ['permission_module_id' => '7', 'code' => 'promotions.viewAny', 'name' => 'View Any Promotions', 'description' => ''],
            ['permission_module_id' => '7', 'code' => 'promotions.create', 'name' => 'Create Promotions', 'description' => ''],
            ['permission_module_id' => '7', 'code' => 'promotions.update', 'name' => 'Update Promotions', 'description' => ''],
            ['permission_module_id' => '7', 'code' => 'promotions.delete', 'name' => 'Delete Promotions', 'description' => ''],
            // merchants
            ['permission_module_id' => '8', 'code' => 'merchants.view', 'name' => 'View Merchant', 'description' => ''],
            ['permission_module_id' => '8', 'code' => 'merchants.viewAny', 'name' => 'View Any Merchants', 'description' => ''],
            ['permission_module_id' => '8', 'code' => 'merchants.create', 'name' => 'Create Merchants', 'description' => ''],
            ['permission_module_id' => '8', 'code' => 'merchants.update', 'name' => 'Update Merchants', 'description' => ''],
            ['permission_module_id' => '8', 'code' => 'merchants.delete', 'name' => 'Delete Merchants', 'description' => ''],
            // announcements
            ['permission_module_id' => '9', 'code' => 'announcements.view', 'name' => 'View Announcement', 'description' => ''],
            ['permission_module_id' => '9', 'code' => 'announcements.viewAny', 'name' => 'View Any Announcements', 'description' => ''],
            ['permission_module_id' => '9', 'code' => 'announcements.create', 'name' => 'Create Announcement', 'description' => ''],
            ['permission_module_id' => '9', 'code' => 'announcements.update', 'name' => 'Update Announcement', 'description' => ''],
            ['permission_module_id' => '9', 'code' => 'announcements.delete', 'name' => 'Delete Announcement', 'description' => ''],
            ['permission_module_id' => '9', 'code' => 'announcements.action', 'name' => 'Action Announcement', 'description' => ''],
            
            // vouchers

            // feedbacks
            ['permission_module_id' => '11', 'code' => 'feedbacks.viewAny', 'name' => 'View Any Feedbacks', 'description' => ''],
            ['permission_module_id' => '11', 'code' => 'feedbacks.delete', 'name' => 'Delete Feedbacks', 'description' => ''],
            // apilogs
            ['permission_module_id' => '12', 'code' => 'apilogs.viewAny', 'name' => 'View Any Feedbacks', 'description' => ''],
        ];

        PermissionModule::insert($permission_modules);
        Permission::insert($permissions);
    }
}
