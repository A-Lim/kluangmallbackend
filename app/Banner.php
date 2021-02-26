<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

use App\Banner;
use Carbon\Carbon;
use App\Http\Traits\CustomQuery;

class Banner extends Model {
    use CustomQuery;

    protected $fillable = ['title', 'status', 'app', 'is_clickable', 'image', 'type', 'type_id', 'created_by', 'updated_by'];
    protected $hidden = [];
    protected $casts = [];

    // list of properties queryable for datatable
    public static $queryable = ['title', 'status', 'type', 'app'];

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const APP_USER = 'user';
    const APP_MERCHANT = 'merchant';

    const TYPE_EVENT = 'event';
    const TYPE_PROMOTION = 'promotion';

    const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    const APPS = [
        self::APP_USER,
        self::APP_MERCHANT,
    ];

    public function getImageAttribute($value) {
        if ($value != null) 
            return json_decode($value);
        
        return $value;
    }
}
