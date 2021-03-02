<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

use App\Event;
use Carbon\Carbon;
use App\Http\Traits\CustomQuery;
use App\Http\Traits\EnvTimezone;

class Event extends Model {
    use CustomQuery, EnvTimezone;

    protected $fillable = ['title', 'thumbnail', 'images', 'fromDate', 'toDate', 'caption', 'content', 'externalLink', 'status', 'created_by', 'updated_by'];
    protected $hidden = [];
    protected $casts = [
        'fromDate' => 'datetime:d M Y',
        'toDate' => 'datetime:d M Y H',
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
        if ($value != null) 
            return json_decode($value);
        
        return $value;
    }

    public function getImagesAttribute($value) {
        if ($value)
            return json_decode($value);

        return null;
    }
}
