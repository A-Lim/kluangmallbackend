<?php
namespace App\Repositories\Promotion;

use App\Promotion;

interface IPromotionRepository {
    /**
     * List all promotions
     * @param array $data
     * @param bool $paginate = false
     * @return array [promotion]
     */
    public function list($data, $paginate = false);

    /**
     * Create Promotion
     * @param array $data
     * @param array $files
     * @return Promotion
     */
    public function create($data, $files);

    /**
     * Update Promotion
     * @param Promotion $promotion
     * @param array $data
     * @param array $files
     * @return Promotion
     */
    public function update(Promotion $promotion, $data, $files);


    /**
     * Delete Promotion
     * @return null
     */
    public function delete(Promotion $promotion);
}