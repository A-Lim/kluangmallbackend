<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class PointTransaction extends Model {

    protected $fillable = ['merchant_id', 'user_id', 'date', 'image', 'amount', 'points'];
    protected $hidden = [];
    protected $casts = [];

    const TYPE_ADD = 'add';
    const TYPE_DEDUCT = 'deduct';

    const TYPES = [
        self::TYPE_ADD,
        self::TYPE_DEDUCT
    ];
}
