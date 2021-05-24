<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Traits\CustomQuery;
use App\Http\Traits\EnvTimezone;
use App\Merchant;

class Voucher extends Model {
    use CustomQuery, EnvTimezone, SoftDeletes;

    protected $fillable = ['merchant_id', 'type', 'display_to_all', 'name', 'image', 'description', 'points', 'free_points', 'status', 'qr', 'data', 'terms_and_conditions', 'has_redemption_limit', 'fromDate', 'toDate', 'deleted_at', 'created_at', 'updated_at', 'created_by', 'updated_by'];
    protected $hidden = [];
    protected $casts = [
        'fromDate' => 'datetime:d M Y',
        'toDate' => 'datetime:d M Y'
    ];

    // list of properties queryable for datatable
    public static $queryable = ['type', 'name', 'status', 'points', 'fromDate', 'toDate'];

    const TYPE_DEDUCT_CASH = 'deduct cash';
    const TYPE_ADD_POINT = 'add point';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_EXPIRED = 'expired';

    const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE
    ];

    const TYPES = [
        self::TYPE_DEDUCT_CASH,
        self::TYPE_ADD_POINT
    ];

    public function merchants() {
        return $this->belongsToMany(Merchant::class);
    }

    public function limits() {
        return $this->hasMany(VoucherLimit::class);
    }

    public function transactions() {
        return $this->hasMany(VoucherTransaction::class);
    }

    public function belongsToMerchant(Merchant $merchant) {
        return $this->merchants()->where('merchants.id', $merchant->id)->exists();
    }

    public function getQrAttribute($value) {
        if ($value != null)
            return json_decode($value);
        
        return null;
    }

    public function getImageAttribute($value) {
        if ($value != null)
            return json_decode($value);
        
        return null;
    }
}
