<?php
namespace App\Repositories\SystemSetting;

use App\SystemSetting;

interface ISystemSettingRepository {
    /**
     * List all systemsettings grouped by systemsettingcategory
     * 
     * @return [SystemSettingCategory]
     */
    public function list();

    /**
     * Update multple systemsettings at once
     * 
     * @param array ['code' => 'value']
     * @return void
     */
    public function update(array $data);

    /**
     * Find by Code
     * 
     * @param string - code
     * @return void
     */
    public function findByCode(string $code);

    /**
     * Find by Codes
     * 
     * @param array string[] - array of codes
     * @return void
     */
    public function findByCodes(array $codes);

    /**
     * Get Mobile App Data (version and update links)
     * 
     * @param array data
     * @return array
     */
    public function appData($data);
}