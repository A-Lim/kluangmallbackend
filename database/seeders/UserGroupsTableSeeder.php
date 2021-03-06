<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\User;
use App\UserGroup;
use Carbon\Carbon;

class UserGroupsTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $now = Carbon::now()->toDateTimeString();
        $userGroups = [
            ['code' => 'developer', 'name' => 'Developer', 'status' => 'active', 'is_admin' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'malladmin', 'name' => 'Mall Admin', 'status' => 'active', 'is_admin' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'mallstaff', 'name' => 'Mall Staff', 'status' => 'active', 'is_admin' => false, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'merchant', 'name' => 'Merchant', 'status' => 'active', 'is_admin' => false, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'user', 'name' => 'User', 'status' => 'active', 'is_admin' => false, 'created_at' => $now, 'updated_at' => $now],
        ];

        UserGroup::insert($userGroups);

        $user1 = User::whereEmail('alexiuslim1994@gmail.com')->firstOrFail();
        $user1->assignUserGroup('developer');

        $user2 = User::whereEmail('malladmin@gmail.com')->firstOrFail();
        $user2->assignUserGroup('malladmin');
    }
}