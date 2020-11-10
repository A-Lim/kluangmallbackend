<?php
namespace App\Repositories\Banner;

use App\Banner;
use Illuminate\Support\Facades\Storage;

class BannerRepository implements IBannerRepository {

    /**
     * {@inheritdoc}
     */
    public function list($data, $paginate = false) {
        $query = Banner::buildQuery($data);

        if ($paginate) {
            $limit = isset($data['limit']) ? $data['limit'] : 10;
            return $query->paginate($limit);
        }

        return $query->get();
    }

    /**
     * {@inheritdoc}
     */
    public function find($id) {
        return Banner::find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function create($files) {
        $files = $files['uploadBanners'];
        $banners = [];

        if (is_array($files)) {
            foreach ($files as $file) {
                array_push($banners, $this->saveBanner($file));
            }
        } else {
            array_push($banners, $this->saveBanner($files));
        }
        

        return $banners;
    }

    /**
     * {@inheritdoc}
     */
    public function update(Banner $banner, $data) {
        $banner->fill($data);
        $banner->save();

        return $banner;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Banner $banner) {
        $folderDir = 'public/banners/'.$banner->id.'/';
        Storage::deleteDirectory($folderDir);
        $banner->delete();
    }

    private function saveBanner($file) {
        $data['created_by'] = auth()->id();
        $data['name'] = $file->getClientOriginalName();
        $data['status'] = Banner::STATUS_ACTIVE;
        $banner = Banner::create($data);

        // upload
        $saveDirectory = 'public/banners/'.$banner->id.'/';
        Storage::putFileAs($saveDirectory, $file, $banner->name);

        $banner->path = Storage::url($saveDirectory.$banner->name);
        $banner->save();

        return $banner;
    }
}