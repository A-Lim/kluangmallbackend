<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\SystemSetting;
use App\SystemSettingCategory;

use Carbon\Carbon;

class SystemSettingsTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $now = Carbon::now();
        $generalCategory = SystemSettingCategory::create(['name' => 'General']);
        $authCategory = SystemSettingCategory::create(['name' => 'Authentication']);
        $socialCategory = SystemSettingCategory::create(['name' => 'Social']);
        $mobileCategory = SystemSettingCategory::create(['name' => 'Mobile']);
        
        $systemsettings = [
            ['systemsettingcategory_id' => $generalCategory->id, 'name' => 'About Us', 'code' => 'about_us', 'description' => '', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['systemsettingcategory_id' => $generalCategory->id, 'name' => 'Contact Us', 'code' => '', 'contact_us' => '', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['systemsettingcategory_id' => $generalCategory->id, 'name' => 'Terms & Conditions', 'code' => 'terms_and_conditions', 'description' => '', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['systemsettingcategory_id' => $generalCategory->id, 'name' => 'Privacy Policy', 'code' => 'privacy_policy', 'description' => '', 'value' => '', 'created_at' => $now, 'updated_at' => $now],

            ['systemsettingcategory_id' => $authCategory->id, 'name' => 'Allow Public Registration', 'code' => 'allow_public_registration', 'description' => 'Allow public users to register to site.', 'value' => true, 'created_at' => $now, 'updated_at' => $now],
            ['systemsettingcategory_id' => $authCategory->id, 'name' => 'Verification Type', 'code' => 'verification_type', 'description' => '', 'value' => 'none', 'created_at' => $now, 'updated_at' => $now],
            ['systemsettingcategory_id' => $authCategory->id, 'name' => 'Default User Group', 'code' => 'default_usergroups', 'description' => '', 'value' => '', 'created_at' => $now, 'updated_at' => $now],

            ['systemsettingcategory_id' => $socialCategory->id, 'name' => 'Facebook', 'code' => 'facebook', 'description' => '', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['systemsettingcategory_id' => $socialCategory->id, 'name' => 'Instagram', 'code' => 'instagram', 'description' => '', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['systemsettingcategory_id' => $socialCategory->id, 'name' => 'Whatsapp', 'code' => 'whatsapp', 'description' => '', 'value' => '', 'created_at' => $now, 'updated_at' => $now],

            ['systemsettingcategory_id' => $mobileCategory->id, 'name' => 'IOS Version', 'code' => 'ios_version', 'description' => '', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['systemsettingcategory_id' => $mobileCategory->id, 'name' => 'IOS Link', 'code' => 'ios_link', 'description' => '', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['systemsettingcategory_id' => $mobileCategory->id, 'name' => 'Android Version', 'code' => 'android_version', 'description' => '', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['systemsettingcategory_id' => $mobileCategory->id, 'name' => 'Android Link', 'code' => 'android_link', 'description' => '', 'value' => '', 'created_at' => $now, 'updated_at' => $now],

            // ['systemsettingcategory_id' => $storeCategory->id, 'name' => 'Currency', 'code' => 'currency', 'description' => '', 'value' => 'USD', 'created_at' => $now, 'updated_at' => $now],
            // ['systemsettingcategory_id' => $paymentCategory->id, 'name' => 'Stripe Key', 'code' => 'stripekey', 'description' => '', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            // ['systemsettingcategory_id' => $paymentCategory->id, 'name' => 'Stripe Secret', 'code' => 'stripesecret', 'description' => '', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            // ['systemsettingcategory_id' => $paymentCategory->id, 'name' => 'PayPal Key', 'code' => 'palpalkey', 'description' => 'PayPal Client ID', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            // ['systemsettingcategory_id' => $paymentCategory->id, 'name' => 'Stripe Secret', 'code' => 'palpalsecret', 'description' => 'Paypal Secret', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
        ];

        SystemSetting::insert($systemsettings);
    }
}
