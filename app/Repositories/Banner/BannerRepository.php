<?php
namespace App\Repositories\Banner;

use App\Banner;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BannerRepository implements IBannerRepository {

    /**
     * {@inheritdoc}
     */
    public function list($data, $paginate = false) {
        $query = null;
        
        if ($data)
            $query = Banner::buildQuery($data);
        else 
            $query = Banner::query();

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
    public function create($data, $files) {
        // boolean data is not recognised when being sent at formdata
        $data['is_clickable'] = $data['is_clickable'] === 'true';
        $banner = Banner::create($data);

        if (isset($files['uploadImage'])) {
            $banner->image = json_encode($this->saveImage($banner, $files['uploadImage']));
        }

        $banner->save();
        return $banner;
    }

    /**
     * {@inheritdoc}
     */
    public function update(Banner $banner, $data, $files) {
        // boolean data is not recognised when being sent at formdata
        $data['is_clickable'] = $data['is_clickable'] === 'true';

        if (isset($files['uploadImage'])) {
            $data['image'] = json_encode($this->saveImage($banner, $files['uploadImage']));
        } else {
            // image property without mutator
            $imageOriginal = json_decode($banner->getAttributes()['image']);
            if ($imageOriginal != null) {
                $fullPath = public_path($imageOriginal->path);
                if (file_exists($fullPath))
                    unlink($fullPath);
            }
            $data['image'] = null;
        }
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

    // private function saveBanner($file) {
    //     $data['created_by'] = auth()->id();
    //     $data['name'] = $file->getClientOriginalName();
    //     $data['status'] = Banner::STATUS_ACTIVE;
    //     $banner = Banner::create($data);

    //     // upload
    //     $saveDirectory = 'public/banners/'.$banner->id.'/';
    //     Storage::putFileAs($saveDirectory, $file, $banner->name);

    //     $banner->path = Storage::url($saveDirectory.$banner->name);
    //     $banner->save();

    //     return $banner;
    // }

    private function saveImage(Banner $banner, UploadedFile $file) {
        $saveDirectory = 'public/banners/'.$banner->id.'/';

        $fileName = $file->getClientOriginalName();
        Storage::putFileAs($saveDirectory, $file, $fileName);

        $data['name'] = $fileName;
        $data['path'] = Storage::url($saveDirectory.$fileName);
        return $data;
    }
}