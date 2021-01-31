<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class Receipt extends Model {

    protected $fillable = ['merchant_id', 'user_id', 'date', 'image', 'amount', 'points'];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'date' => 'datetime:d M Y',
    ];
}
