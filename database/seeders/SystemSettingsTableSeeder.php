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
        $creditCategory = SystemSettingCategory::create(['name' => 'Credit']);
        $redemptionCategory = SystemSettingCategory::create(['name' => 'Redemption']);
        
        $systemsettings = [
            ['systemsettingcategory_id' => $generalCategory->id, 'name' => 'Email', 'code' => 'email', 'description' => '', 'value' => ''],
            ['systemsettingcategory_id' => $generalCategory->id, 'name' => 'Phone', 'code' => 'phone', 'description' => '', 'value' => ''],
            ['systemsettingcategory_id' => $generalCategory->id, 'name' => 'Address', 'code' => 'address', 'description' => '', 'value' => ''],
            ['systemsettingcategory_id' => $generalCategory->id, 'name' => 'About Us', 'code' => 'about_us', 'description' => '', 'value' => ''],
            ['systemsettingcategory_id' => $generalCategory->id, 'name' => 'Contact Us', 'code' => '', 'contact_us' => '', 'value' => ''],
            ['systemsettingcategory_id' => $generalCategory->id, 'name' => 'Terms & Conditions', 'code' => 'terms_and_conditions', 'description' => '', 'value' => ''],
            ['systemsettingcategory_id' => $generalCategory->id, 'name' => 'Privacy Policy', 'code' => 'privacy_policy', 'description' => '', 'value' => ''],

            ['systemsettingcategory_id' => $authCategory->id, 'name' => 'Allow Public Registration', 'code' => 'allow_public_registration', 'description' => 'Allow public users to register to site.', 'value' => false],
            ['systemsettingcategory_id' => $authCategory->id, 'name' => 'Verification Type', 'code' => 'verification_type', 'description' => '', 'value' => SystemSetting::VTYPE_EMAIL],
            ['systemsettingcategory_id' => $authCategory->id, 'name' => 'Default User Group', 'code' => 'default_usergroups', 'description' => '', 'value' => '[5]'],

            ['systemsettingcategory_id' => $socialCategory->id, 'name' => 'Facebook', 'code' => 'facebook', 'description' => '', 'value' => ''],
            ['systemsettingcategory_id' => $socialCategory->id, 'name' => 'Instagram', 'code' => 'instagram', 'description' => '', 'value' => ''],
            ['systemsettingcategory_id' => $socialCategory->id, 'name' => 'Whatsapp', 'code' => 'whatsapp', 'description' => '', 'value' => ''],

            ['systemsettingcategory_id' => $mobileCategory->id, 'name' => 'User App IOS Version', 'code' => 'user_ios_version', 'description' => '', 'value' => '1.0.0'],
            ['systemsettingcategory_id' => $mobileCategory->id, 'name' => 'User App IOS Link', 'code' => 'user_ios_link', 'description' => '', 'value' => 'user.ios.com'],
            ['systemsettingcategory_id' => $mobileCategory->id, 'name' => 'User App Android Version', 'code' => 'user_android_version', 'description' => '', 'value' => '1.0.0'],
            ['systemsettingcategory_id' => $mobileCategory->id, 'name' => 'User App Android Link', 'code' => 'user_android_link', 'description' => '', 'value' => 'user.android.com'],

            ['systemsettingcategory_id' => $mobileCategory->id, 'name' => 'Merchant App IOS Version', 'code' => 'merchant_ios_version', 'description' => '', 'value' => '1.0.0'],
            ['systemsettingcategory_id' => $mobileCategory->id, 'name' => 'Merchant App IOS Link', 'code' => 'merchant_ios_link', 'description' => '', 'value' => 'merchant.ios.com'],
            ['systemsettingcategory_id' => $mobileCategory->id, 'name' => 'Merchant App Android Version', 'code' => 'merchant_android_version', 'description' => '', 'value' => '1.0.0'],
            ['systemsettingcategory_id' => $mobileCategory->id, 'name' => 'Merchant App Android Link', 'code' => 'merchant_android_link', 'description' => '', 'value' => 'merchant.android.com'],

            ['systemsettingcategory_id' => $creditCategory->id, 'name' => 'Announcement', 'code' => 'announcement', 'description' => 'Credits required per announcement.', 'value' => '5'],

            ['systemsettingcategory_id' => $redemptionCategory->id, 'name' => 'Points Rate', 'code' => 'points_rate', 'description' => 'Points awarded for every RM1', 'value' => ''],
        ];

        SystemSetting::insert($systemsettings);
    }
}
