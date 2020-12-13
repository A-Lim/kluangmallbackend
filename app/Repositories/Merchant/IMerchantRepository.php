<?php
namespace App\Repositories\Merchant;

use App\Merchant;
use App\MerchantCategory;

interface IMerchantRepository {
    /**
     * List all merchants
     * @param array $data 
     * @param bool $paginate
     * @return array [Merchant]
     */
    public function list($data, $paginate = false);

    /**
     * List all merchant categories
     * @param array $data 
     * @param bool $paginate
     * @return array [Merchant]
     */
    public function listCategories($data, $paginate = false);

    /**
     * Get Merchant Details
     * @param integer $id 
     * @param bool $withDetails
     * @return array Merchant
     */
    public function find($id, $withDetails);

    /**
     * Track page visit for merchant page
     * @param Merchant $merchant
     * @param array $data
     * @return null
     */
    public function track($data);

    /**
     * Create merchant
     * @param array $data 
     * @param array $files
     * @return Merchant
     */
    public function create($data, $files);

    /**
     * Update merchant
     * Merchant $merchant
     * @param array $data 
     * @param array $files
     * @return Merchant
     */
    public function update(Merchant $merchant, $data, $files);

    /**
     * Create merchant users
     * Merchant $merchant
     * @param array $data 
     */
    public function createUsers(Merchant $merchant, $data);

    /**
     * Delete merchant category
     * MerchantCategory $merchantCategory
     * @return null
     */
    public function deleteMerchantCategory(MerchantCategory $merchantCategory);
}