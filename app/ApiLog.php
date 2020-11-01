<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\CustomQuery;

class ApiLog extends Model {
    use CustomQuery;

    protected $fillable = ['user_id', 'method', 'url', 'ip', 'user_agent', 'header', 'request_data', 'response_data', 'status'];
    protected $hidden = [];
    protected $casts = [];
}
