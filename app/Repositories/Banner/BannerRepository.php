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
        $folderDir = 'banners/'.$banner->id.'/';
        Storage::disk('s3')->deleteDirectory($folderDir);
        $banner->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function removeIsClickable($type, $type_id) {
        Banner::where('type', $type)
            ->where('type_id', $type_id)
            ->update([
                'is_clickable' => false,
                'type' => null,
                'type_id' => null
            ]);
    }

    private function saveImage(Banner $banner, UploadedFile $file) {
        $saveDirectory = 'banners/'.$banner->id.'/';

        $fileName = $file->getClientOriginalName();
        Storage::disk('s3')->putFileAs($saveDirectory, $file, $fileName, 'public');

        $data['name'] = $fileName;
        $data['path'] = Storage::disk('s3')->url($saveDirectory.$fileName);
        return $data;
    }

    private function deleteImage(Banner $banner) {
        // image property without mutator
        $imgOriginal = json_decode($banner->getAttributes()['image']);

        if ($imgOriginal != null)
            Storage::disk('s3')->delete('banners/'.$banner->id.'/'.$imgOriginal->name);
    }
}