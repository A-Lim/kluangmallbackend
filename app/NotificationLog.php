<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model {
    protected $fillable = ['user_id', 'notification_log_id', 'title', 'content', 'read'];
    protected $hidden = [];
    protected $casts = [];
}
