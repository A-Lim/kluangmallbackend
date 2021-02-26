<?php
namespace App\Repositories\Banner;

use App\Banner;

interface IBannerRepository {
    /**
     * List all banners
     * @param array $data
     * @param bool $paginate = false
     * @return array [banner]
     */
    public function list($data, $paginate = false);

    /**
     * Create Banners
     * @param array $data
     * @param array $files
     * @return [Banner]
     */
    public function create($data, $files);

    /**
     * Update Banner Details
     * @param Banner $banner
     * @param array $data
     * @param array $files
     * @return Banner
     */
    public function update(Banner $banner, $data, $files);

    /**
     * Remove isClickable
     * @param $type
     * @param $type_id
     * @return null
     */
    public function removeIsClickable($type, $type_id);

    /**
     * Delete Banner
     * @return null
     */
    public function delete(Banner $banner);
}