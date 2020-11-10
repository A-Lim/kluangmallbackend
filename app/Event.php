<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

use App\Event;
use Carbon\Carbon;
use App\Http\Traits\CustomQuery;

class Event extends Model {
    use CustomQuery;

    protected $fillable = ['title', 'category', 'thumbnail', 'images', 'fromDate', 'toDate', 'content', 'externalLink', 'status', 'created_by', 'updated_by'];
    protected $hidden = [];
    protected $casts = [
        'fromDate' => 'datetime:d-m-Y',
        'toDate' => 'datetime:d-m-Y',
    ];

    // list of properties queryable for datatable
    public static $queryable = ['title', 'category', 'fromDate', 'toDate', 'status'];

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    public function setFromDateAttribute($value) {
        $this->attributes['fromDate'] = Carbon::parse($value);
    }

    public function setToDateAttribute($value) {
        $this->attributes['toDate'] = Carbon::parse($value);
    }

    public function getThumbnailAttribute($value) {
        if ($value != null) {
            $data = json_decode($value);
            $thumbNail['name'] = $data->name;
            $thumbNail['path'] = URL::to('/').$data->path;
            return $thumbNail;
        }
        return $value;
    }

    public function getImagesAttribute($value) {
        $images = [];
        if ($value) {
            $data = json_decode($value);
            foreach ($data as $image) {
                $imageData['name'] = $image->name;
                $imageData['path'] = URL::to('/').$image->path;
                array_push($images, $imageData);
            }
        }
        return $images;
    }
}
