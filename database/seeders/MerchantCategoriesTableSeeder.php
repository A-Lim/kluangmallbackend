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
            ['name' => 'FASHION SPECIALTY / WOMEN / CHILDREN / ACCESSORIES', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'FOOD / BEVERAGES / SNACKS', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'HEALTH / BEAUTY', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'GIFT / BOOKS / TOYS / STATIONERY', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'LEISURE / ENTERTAINMENT', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'SHOES / BAGS / ACCESSORIES ', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'HOMEWARES & FURNISHINGS', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'JEWELLERY / TIMEPIECES / OPTICAL', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'ELECTRONICS, IT & APPLIANCES', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'SPORT / FITNESS', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'SERVICES', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'HYPERMARKET', 'created_at' => $now, 'updated_at' => $now],
        ];

        MerchantCategory::insert($categories);
    }
}
