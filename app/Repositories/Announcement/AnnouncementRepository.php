<?php
namespace App\Repositories\Announcement;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use DB;
use Carbon\Carbon;
use App\User;
use App\Merchant;
use App\Announcement;

class AnnouncementRepository implements IAnnouncementRepository {

    /**
     * {@inheritdoc}
     */
    public function list($data, $paginate = false) {
        $query = null;
        
        if ($data)
            $query = Announcement::buildQuery($data);
        else 
            $query = Announcement::query()->orderBy('id', 'desc');

        $query = $query->leftJoin('merchants' , 'merchants.id', '=', 'announcements.merchant_id')
            ->select('announcements.*', 'merchants.name as merchant_name');

        // searching in join table
        if (isset($data['merchant_name'])) {
            $filterData = explode(':', $data['merchant_name']);
            $filterType = strtolower($filterData[0]);
            $filterVal  = $filterData[1];

            if ($filterType == 'contains')
                $query->where('merchants.name', 'LIKE', '%'.$filterVal.'%');
            
            if ($filterType == 'equals')
                $query->where('merchants.name', $filterVal);
        }

        // sort where status pending at the top
        $query->orderBy('id', 'desc');
            // ->orderByRaw("FIELD(announcements.status , 'pending', 'published', 'rejected') ASC");

        if ($paginate) {
            $limit = isset($data['limit']) ? $data['limit'] : 10;
            return $query->paginate($limit);
        }

        return $query->get();
    }

    /**
     * {@inheritdoc}
     */
    public function pendingCount() {
        return Announcement::where('status', Announcement::STATUS_PENDING)->count();
    }
    
    /**
     * {@inheritdoc}
     */
    public function create($data, $credit_paid, Merchant $merchant = null, $files = null) {
        if ($merchant)
            $data['merchant_id'] = $merchant->id;

        // boolean data is not recognised when being sent at formdata
        $data['has_content'] = $data['has_content'] === 'true';
        $data['requested_by'] = auth()->id();
        $data['credit_paid'] = $credit_paid;
        
        if (isset($data['publish_now']) && (bool)$data['publish_now']) {
            $data['publish_at'] = Carbon::today();
            $data['status'] = Announcement::STATUS_PUBLISHED;
        }
        else {
            $data['publish_at'] = Carbon::createFromFormat(env('DATE_FORMAT'), $data['publish_at']);
        }

        $announcement = Announcement::create($data);

        if (isset($files['uploadImage']))
            $announcement->image = json_encode($this->saveImage($announcement, $files['uploadImage']));

        $announcement->save();
        return $announcement;
    }

    /**
     * {@inheritdoc}
     */
    public function update(Announcement $announcement, $data, $files = null) {
        // boolean data is not recognised when being sent at formdata
        $data['has_content'] = $data['has_content'] === 'true';
        $data['publish_at'] = Carbon::createFromFormat(env('DATE_FORMAT'), $data['publish_at']);
        
        if (isset($files['uploadImage'])) {
            $this->deleteImage($announcement);
            $data['image'] = json_encode($this->saveImage($announcement, $files['uploadImage']));
        } else if (!isset($files['uploadImage']) && !isset($data['image'])) {
            $this->deleteImage($announcement);
            $data['image'] = null;
        } else {
            $data['image'] = $announcement->getAttributes()['image'];
        }

        $announcement->fill($data);
        $announcement->save();

        return $announcement;
    }

    public function approve(Announcement $announcement, $data) {
        $announcement->actioned_by = auth()->id();
        $announcement->status = Announcement::STATUS_APPROVED;
        $announcement->remark = @$data['remark'];
        $announcement->save();

        return $announcement;
    }

    public function reject(Announcement $announcement, $data) {
        $announcement->actioned_by = auth()->id();
        $announcement->status = Announcement::STATUS_REJECTED;
        $announcement->remark = @$data['remark'];
        $announcement->save();
        
        return $announcement;
    }

    private function saveImage(Announcement $announcement, UploadedFile $file) {
        $saveDirectory = 'public/announcements/'.$announcement->id.'/';

        $fileName = $file->getClientOriginalName();
        Storage::putFileAs($saveDirectory, $file, $fileName);

        $data['name'] = $fileName;
        $data['path'] = Storage::url($saveDirectory.$fileName);
        return $data;
    }

    private function deleteImage(Announcement $announcement) {
        // image property without mutator
        $imgOriginal = json_decode($announcement->getAttributes()['image']);

        if ($imgOriginal != null) {
            $fullPath = public_path($imgOriginal->path);
            if (file_exists($fullPath))
                unlink($fullPath);
        }
    }
}