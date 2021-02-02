<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class PointTransaction extends Model {

    protected $fillable = ['merchant_id', 'user_id', 'receipt_id', 'type', 'image', 'amount', 'points', 'description'];
    protected $hidden = [];
    protected $casts = [];

    const TYPE_ADD = 'add';
    const TYPE_DEDUCT = 'deduct';

    const TYPES = [
        self::TYPE_ADD,
        self::TYPE_DEDUCT
    ];

    public function receipt() {
        return $this->belongsTo(Receipt::class);
    }
}
