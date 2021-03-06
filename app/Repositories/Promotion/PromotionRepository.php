<?php
namespace App\Repositories\Promotion;

use DB;
use App\Promotion;
use Carbon\Carbon;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PromotionRepository implements IPromotionRepository {

    /**
     * {@inheritdoc}
     */
    public function list($data, $paginate = false) {
        $query = null;
        
        if ($data)
            $query = Promotion::buildQuery($data);
        else 
            $query = Promotion::query()->orderBy('id', 'desc');

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
        return Promotion::find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function create($data, $files) {
        $data['created_by'] = auth()->id();
        DB::beginTransaction();
        $promotion = Promotion::create($data);

        if (isset($files['uploadThumbnail']))
            $promotion->thumbnail = json_encode($this->saveImage($promotion, $files['uploadThumbnail'], true));
        else
            $promotion->thumbnail = null;
        
        if (isset($files['uploadImages']))
            $promotion->images = json_encode($this->saveImages($promotion, $files['uploadImages']));
        else 
            $promotion->images = null;

        $promotion->save();
        DB::commit();

        return $promotion;
    }

    /**
     * {@inheritdoc}
     */
    public function update(Promotion $promotion, $data, $files) {
        $imgToBeDeleted = [];
        // existing images
        $existingImages = json_decode($promotion->getAttributes()['images'], true) ?? [];

        if (isset($data['images'])) {
            // get images that are inside $existingImages but not inside $data['images']
            $imgToBeDeleted = array_udiff($existingImages, $data['images'],
                function ($a, $b) {
                    if ($a['name'] == $b['name'])
                        return 0;

                    return $a['name'] > $b['name'] ? 1 : -1;
                }
            );
        } else {
            // delete all existing
            $imgToBeDeleted = $existingImages;
        }

        // delete files 
        foreach ($imgToBeDeleted as $image) {
            Storage::disk('s3')->delete('promotions/'.$promotion->id.'/'.$image['name']);
            
            foreach ($existingImages as $index => $existingImage) {
                if ($existingImage['name'] == $image['name']) {
                    unset($existingImages[$index]);
                }
            }
        }

        // save new files
        $newImages = [];
        if (isset($files['uploadImages'])) 
            $newImages = $this->saveImages($promotion, $files['uploadImages']);
        
        $allImages = collect(array_merge($newImages, $existingImages))->unique();

        // update thumbnail
        $thumbnailOriginal = json_decode($promotion->getAttributes()['thumbnail']);
        // delete existing thumbnail
        if (!isset($data['thumbnail'])) {
            if ($thumbnailOriginal != null) {
                Storage::disk('s3')->delete('promotions/'.$promotion->id.'/'.$thumbnailOriginal->name);
            }
            $data['thumbnail'] = null;
        }
        
        // retain existing
        if (isset($data['thumbnail']))
            $data['thumbnail'] = $promotion->getAttributes()['thumbnail'];
        
        // upload new
        if (isset($files['uploadThumbnail']))
            $data['thumbnail'] = json_encode($this->saveImage($promotion, $files['uploadThumbnail'], true));
        
        $data['images'] = json_encode($allImages);
        $data['updated_by'] = auth()->id();

        $promotion->fill($data);
        $promotion->save();
        return $promotion;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Promotion $promotion) {
        $folderDir = 'promotions/'.$promotion->id.'/';
        Storage::disk('s3')->deleteDirectory($folderDir);
        $promotion->delete();
    }

    private function saveImages(Promotion $promotion, $files) {
        if ($files == null || count($files) == 0)
            return;
        
        $fileDetails = [];
        foreach ($files as $file) {
            array_push($fileDetails, $this->saveImage($promotion, $file, false));
        }

        return $fileDetails;
    }

    private function saveImage(Promotion $promotion, UploadedFile $file, $isThumbnail) {
        $saveDirectory = 'promotions/'.$promotion->id.'/';

        if ($isThumbnail)
            $saveDirectory = $saveDirectory.'thumbnails/';

        $fileName = $file->getClientOriginalName();
        Storage::disk('s3')->putFileAs($saveDirectory, $file, $fileName, 'public');

        $data['name'] = $fileName;
        $data['path'] = Storage::disk('s3')->url($saveDirectory.$fileName);
        return $data;
    }
}