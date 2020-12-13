<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use App\Http\Traits\CustomQuery;

class Feedback extends Model {
    use CustomQuery;

    protected $table = 'feedbacks';
    protected $fillable = ['name', 'email', 'subject', 'message'];
    protected $hidden = [];
    protected $casts = [
        'created_at' => 'datetime:d M Y H:i:s',
    ];

    // list of properties queryable for datatable
    public static $queryable = ['name', 'email', 'subject', 'created_at'];
}
