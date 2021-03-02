<?php
namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Http\Traits\CustomQuery;
use App\Http\Traits\EnvTimezone;

class Notification extends Model {
    use CustomQuery, EnvTimezone;

    protected $fillable = ['user_id', 'notification_log_id', 'title', 'description', 'payload', 'read'];
    protected $hidden = ['user_id', 'notification_log_id'];
    protected $casts = [
        'read' => 'boolean',
        'created_at' => 'datetime:d M Y H:i a',
    ];

    // list of properties queryable for datatable
    public static $queryable = ['title', 'description', 'payload', 'read'];
}
