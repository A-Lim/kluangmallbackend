<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model {
    protected $fillable = ['request_data', 'response_data', 'status'];
    protected $hidden = [];
    protected $casts = [];
}
