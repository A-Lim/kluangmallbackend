<?php
namespace App\Repositories\Event;

use DB;
use App\Event;
use Carbon\Carbon;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EventRepository implements IEventRepository {

    /**
     * {@inheritdoc}
     */
    public function list($data, $paginate = false) {
        $query = null;
        
        if ($data)
            $query = Event::buildQuery($data);
        else 
            $query = Event::query()->orderBy('id', 'desc');

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
        return Event::find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function create($data, $files) {
        $data['created_by'] = auth()->id();
        DB::beginTransaction();
        $event = Event::create($data);

        if (isset($files['uploadThumbnail'])) 
            $event->thumbnail = json_encode($this->saveImage($event, $files['uploadThumbnail'], true));
        else 
            $event->thumbnail = null;
        
        if (isset($files['uploadImages']))
            $event->images = json_encode($this->saveImages($event, $files['uploadImages']));
        else 
            $event->images = null;
        
        $event->save();
        DB::commit();

        return $event;
    }

    /**
     * {@inheritdoc}
     */
    public function update(Event $event, $data, $files) {
        $imgToBeDeleted = [];
        // existing images
        $existingImages = json_decode($event->getAttributes()['images'], true) ?? [];

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
            $fullPath = public_path($image['path']);
            if (file_exists($fullPath))
                unlink($fullPath);
            
            foreach ($existingImages as $index => $existingImage) {
                if ($existingImage['name'] == $image['name']) {
                    unset($existingImages[$index]);
                }
            }
        }

        // save new files
        $newImages = [];
        if (isset($files['uploadImages'])) 
            $newImages = $this->saveImages($event, $files['uploadImages']);
        
        $allImages = collect(array_merge($newImages, $existingImages))->unique();

        // update thumbnail
        $thumbnailOriginal = json_decode($event->getAttributes()['thumbnail']);
        // delete existing thumbnail
        if (!isset($data['thumbnail'])) {
            if ($thumbnailOriginal != null) {
                $fullPath = public_path($thumbnailOriginal->path);
                if (file_exists($fullPath))
                    unlink($fullPath);
            }
            $data['thumbnail'] = null;
        }
        
        // retain existing
        if (isset($data['thumbnail']))
            $data['thumbnail'] = $event->getAttributes()['thumbnail'];
        
        // upload new
        if (isset($files['uploadThumbnail']))
            $data['thumbnail'] = json_encode($this->saveImage($event, $files['uploadThumbnail'], true));
        
        $data['images'] = json_encode($allImages);
        $data['updated_by'] = auth()->id();

        $event->fill($data);
        $event->save();
        return $event;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Event $event) {
        $folderDir = 'public/events/'.$event->id.'/';
        Storage::deleteDirectory($folderDir);
        $event->delete();
    }

    private function saveImages(Event $event, $files) {
        if ($files == null || count($files) == 0)
            return;
        
        $fileDetails = [];
        foreach ($files as $file) {
            array_push($fileDetails, $this->saveImage($event, $file, false));
        }

        return $fileDetails;
    }

    private function saveImage(Event $event, UploadedFile $file, $isThumbnail) {
        $saveDirectory = 'public/events/'.$event->id.'/';

        if ($isThumbnail)
            $saveDirectory = $saveDirectory.'thumbnails/';

        $fileName = $file->getClientOriginalName();
        Storage::putFileAs($saveDirectory, $file, $fileName);

        $data['name'] = $fileName;
        $data['path'] = Storage::url($saveDirectory.$fileName);
        return $data;
    }
}