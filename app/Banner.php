<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

use App\Banner;
use Carbon\Carbon;
use App\Http\Traits\CustomQuery;

class Banner extends Model {
    use CustomQuery;

    protected $fillable = ['title', 'status', 'is_clickable', 'image', 'type', 'type_id'];
    protected $hidden = [];
    protected $casts = [];

    // list of properties queryable for datatable
    public static $queryable = ['title', 'status', 'type'];

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    public function getImageAttribute($value) {
        if ($value != null) {
            $data = json_decode($value);
            $thumbNail['name'] = $data->name;
            $thumbNail['path'] = URL::to('/').$data->path;
            return $thumbNail;
        }
        return $value;
    }
}
