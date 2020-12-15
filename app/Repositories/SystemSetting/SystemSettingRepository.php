<?php
namespace App\Repositories\SystemSetting;

use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

use App\SystemSetting;
use App\SystemSettingCategory;

class SystemSettingRepository implements ISystemSettingRepository {

     /**
     * {@inheritdoc}
     */
    public function list() {
        return Cache::rememberForEver(SystemSettingCategory::CACHE_KEY, function() {
            return SystemSettingCategory::with('systemsettings')->get();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function findByCode(string $code) {
        return Cache::rememberForEver(SystemSetting::CACHE_KEY.'_'.$code, function() use ($code) {
            return SystemSetting::where('code', $code)->first();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function findByCodes(array $codes) {
        return SystemSetting::whereIn('code', $codes)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function appData($data) {
        $systemsettings = null;
        $result = [];
        switch ($data['os']) {
            case 'android':
                if ($data['type'] == 'merchant')
                    $systemsettings = SystemSetting::whereIn('code', ['merchant_android_version', 'merchant_android_link'])->get();
                else
                    $systemsettings = SystemSetting::whereIn('code', ['user_android_version', 'user_android_link'])->get();
                break;
            
            case 'ios':
                if ($data['type'] == 'merchant')
                    $systemsettings = SystemSetting::whereIn('code', ['merchant_ios_version', 'merchant_ios_link'])->get();
                else
                    $systemsettings = SystemSetting::whereIn('code', ['user_ios_version', 'user_ios_link'])->get();
                
                break;
        }

        foreach ($systemsettings as $systemsetting) {
            if (in_array($systemsetting->code, ['merchant_android_version', 'merchant_ios_version', 'user_android_version', 'user_ios_version']))
                $result['version'] = $systemsetting->value;

            if (in_array($systemsetting->code, ['merchant_android_link', 'merchant_ios_link', 'user_android_link', 'user_ios_link']))
                $result['link'] = $systemsetting->value;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $data) {
        // https://github.com/laravel/ideas/issues/575
        // update multiple values based on code
        // ['code' => 'value']
        DB::transaction(function () use ($data) {
            $table = SystemSetting::getModel()->getTable();
            $cases = [];
            $codes = [];
            $params = [];

            foreach ($data as $code => $value) {
                $cases[] = "WHEN `code` = '{$code}' then ?";
                $codes[] = $code;

                // codes where values should be json encoded
                $json_fields = ['default_usergroups'];
                if (in_array($code, $json_fields))
                    $params[] = json_encode($value);
                else
                    $params[] = $value;
            }

            $codes = "'".implode('\',\'', $codes)."'";
            $cases = implode(' ', $cases);
            // $params[] = Carbon::now();
            DB::update("UPDATE `{$table}` SET `value` = CASE {$cases} END WHERE `code` in ({$codes})", $params);

            // cache keys are codes
            $cacheKeys = array_keys($data);
            $this->clearCaches($cacheKeys);
        });
    }

    /**
     * Clear all cache
     * 
     * @param array $codes 
     * @return void
     */
    private function clearCaches(array $codes = null) {
        Cache::forget(SystemSettingCategory::CACHE_KEY);
        if (!empty($codes)) {
            foreach ($codes as $code) {
                Cache::forget(SystemSetting::CACHE_KEY.'_'.$code);
            }
        }
    }
}