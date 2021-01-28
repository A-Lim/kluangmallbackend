<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Traits\CustomQuery;

class Voucher extends Model {
    use CustomQuery, SoftDeletes;

    protected $fillable = ['name', 'description', 'merchant_id', 'points', 'status', 'qr', 'data', 'terms_and_conditions', 'has_redemption_limit', 'fromDate', 'toDate', 'deleted_at', 'created_at', 'updated_at', 'created_by', 'updated_by'];
    protected $hidden = [];
    protected $casts = [
        'fromDate' => 'datetime:d M Y',
        'toDate' => 'datetime:d M Y'
    ];

    // list of properties queryable for datatable
    public static $queryable = ['name', 'status', 'points', 'fromDate', 'toDate'];

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE
    ];

    public function merchant() {
        return $this->belongsTo(Merchant::class);
    }

    public function limits() {
        return $this->hasMany(VoucherLimit::class);
    }

    public function transactions() {
        return $this->hasMany(VoucherTransaction::class);
    }

    public function getQrAttribute($value) {
        if ($value != null)
            return json_decode($value);
        
        return null;
    }
}