<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\MerchantCategory;
use Carbon\Carbon;

class MerchantCategoriesTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $now = Carbon::now();
        
        $categories = [
            ['name' => 'Amentities', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Anchors', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Bags & Leather Goods', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Banking', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Beauty', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Books & Stationery', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Convenience Store', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Digital Lifestyle', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Entertainment', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Eyewear', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Fashion', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Food & Beverages', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Gift', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Haircare / Salon', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Health & Wellness', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Home & Living', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Jewellery & Timepieces', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Money Changer', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Photography', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Services', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Stationery', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Toys & Hobbies', 'created_at' => $now, 'updated_at' => $now],
        ];

        MerchantCategory::insert($categories);
    }
}
