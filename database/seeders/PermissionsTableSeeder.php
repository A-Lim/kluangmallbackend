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
            ['code' => 'events', 'name' => 'Events', 'description' => 'Events module', 'is_active' => '1'],
            ['code' => 'promotions', 'name' => 'Promotions', 'description' => 'Promotions module', 'is_active' => '1'],
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
            // events
            ['permission_module_id' => '4', 'code' => 'events.view', 'name' => 'View Event', 'description' => ''],
            ['permission_module_id' => '4', 'code' => 'events.viewAny', 'name' => 'View Any Events', 'description' => ''],
            ['permission_module_id' => '4', 'code' => 'events.create', 'name' => 'Create Events', 'description' => ''],
            ['permission_module_id' => '4', 'code' => 'events.delete', 'name' => 'Delete Events', 'description' => ''],
            ['permission_module_id' => '4', 'code' => 'events.update', 'name' => 'Update Events', 'description' => ''],
            // promotions
            ['permission_module_id' => '5', 'code' => 'promotions.view', 'name' => 'View Promotion', 'description' => ''],
            ['permission_module_id' => '5', 'code' => 'promotions.viewAny', 'name' => 'View Any Promotions', 'description' => ''],
            ['permission_module_id' => '5', 'code' => 'promotions.create', 'name' => 'Create Promotions', 'description' => ''],
            ['permission_module_id' => '5', 'code' => 'promotions.update', 'name' => 'Update Promotions', 'description' => ''],
            ['permission_module_id' => '5', 'code' => 'promotions.delete', 'name' => 'Delete Promotions', 'description' => ''],

        ];

        PermissionModule::insert($permission_modules);
        Permission::insert($permissions);
    }
}
