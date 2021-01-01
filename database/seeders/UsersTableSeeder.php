<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        User::insert([
            [
                'name' => 'Alexius',
                'email' => 'alexiuslim1994@gmail.com',
                'member_no' => mt_rand(1000000000, 9999999999),
                'gender' => 'male',
                'password' => Hash::make('123456789'),
                'status' => User::STATUS_ACTIVE,
            ],
            [
                'name' => 'Mall Admin User',
                'email' => 'malladmin@gmail.com',
                'member_no' => mt_rand(1000000000, 9999999999),
                'gender' => 'male',
                'password' => Hash::make('123456789'),
                'status' => User::STATUS_ACTIVE,
            ]
        ]);
    }
}
