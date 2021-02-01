<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MyVoucher extends Model {

    protected $table = 'myvouchers';
    protected $fillable = ['voucher_id', 'user_id', 'merchant_id', 'expiry_date', 'status'];
    protected $hidden = [];
    protected $casts = [
        'expiry_date' => 'datetime:d M Y',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_USED = 'used';
    const STATUS_EXPIRED = 'expired';

    const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_USED,
        self::STATUS_EXPIRED,
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function merchant() {
        return $this->belongsTo(Merchant::class);
    }

    public function voucher() {
        return $this->belongsTo(Voucher::class);
    }

    public function getImageAttribute($value) {
        if ($value != null)
            return json_decode($value);
        
        return null;
    }
}
