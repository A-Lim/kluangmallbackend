<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class PointTransaction extends Model {

    protected $fillable = ['merchant_id', 'user_id', 'receipt_id', 'type', 'image', 'amount', 'points', 'description'];
    protected $hidden = ['updated_at', 'receipt_id'];
    protected $casts = [
        'created_at' => 'datetime:d M Y H:i a',
    ];

    const TYPE_ADD = 'add';
    const TYPE_DEDUCT = 'deduct';
    const TYPE_PENDING = 'pending';

    const TYPES = [
        self::TYPE_ADD,
        self::TYPE_DEDUCT,
        self::TYPE_PENDING
    ];

    public function receipt() {
        return $this->belongsTo(Receipt::class);
    }
}
