<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Casts\DateTimeFormat;
use App\Http\Traits\CustomQuery;

class Feedback extends Model {
    use CustomQuery;

    protected $table = 'feedbacks';
    protected $fillable = ['name', 'email', 'subject', 'message'];
    protected $hidden = [];
    protected $casts = [
        'created_at' => DateTimeFormat::class,
        'updated_at' => DateTimeFormat::class
    ];

    // list of properties queryable for datatable
    public static $queryable = ['name', 'email', 'subject', 'created_at'];
}
