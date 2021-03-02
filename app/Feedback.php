<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Casts\DateTimeFormat;
use App\Http\Traits\CustomQuery;
use Carbon\Carbon;
use App\Http\Traits\EnvTimezone;

class Feedback extends Model {
    use CustomQuery, EnvTimezone;

    protected $table = 'feedbacks';
    protected $fillable = ['name', 'email', 'subject', 'message'];
    protected $hidden = [];
    protected $casts = [
        'created_at' => 'datetime:d M Y H:i a',
        'updated_at' => 'datetime:d M Y H:i a'
    ];

    // list of properties queryable for datatable
    public static $queryable = ['name', 'email', 'subject', 'created_at'];
}
