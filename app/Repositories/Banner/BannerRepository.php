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
            $query = Banner::query()->orderBy('id', 'desc');

        $query->orderBy('id', 'desc');
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
        $data['created_by'] = auth()->id();
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
        $data['updated_by'] = auth()->id();

        if (isset($files['uploadImage'])) {
            $this->deleteImage($banner);
            $data['image'] = json_encode($this->saveImage($banner, $files['uploadImage']));
        } else if (!isset($files['uploadImage']) && !isset($data['image'])) {
            $this->deleteImage($banner);
            $data['image'] = null;
        } else {
            $data['image'] = $banner->getAttributes()['image'];
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

    private function saveImage(Banner $banner, UploadedFile $file) {
        $saveDirectory = 'public/banners/'.$banner->id.'/';

        $fileName = $file->getClientOriginalName();
        Storage::putFileAs($saveDirectory, $file, $fileName);

        $data['name'] = $fileName;
        $data['path'] = Storage::url($saveDirectory.$fileName);
        return $data;
    }

    private function deleteImage(Banner $banner) {
        // image property without mutator
        $imgOriginal = json_decode($banner->getAttributes()['image']);

        if ($imgOriginal != null) {
            $fullPath = public_path($imgOriginal->path);
            if (file_exists($fullPath))
                unlink($fullPath);
        }
    }
}