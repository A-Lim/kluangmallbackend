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
     * @param array $files
     * @return [Banner]
     */
    public function create($files);

    /**
     * Update Banner Details
     * @param Banner $banner
     * @param array $data
     * @return Banner
     */
    public function update(Banner $banner, $data);


    /**
     * Delete Banner
     * @return null
     */
    public function delete(Banner $banner);
}