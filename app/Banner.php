<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

use App\Banner;
use Carbon\Carbon;
use App\Http\Traits\CustomQuery;

class Banner extends Model {
    use CustomQuery;

    protected $fillable = ['name', 'path', 'status', 'created_by'];
    protected $hidden = [];
    protected $casts = [];

    // list of properties queryable for datatable
    public static $queryable = ['name', 'status'];

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    public function getPathAttribute($value) {
        return URL::to('/').$value;
    }
}
