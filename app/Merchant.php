<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Notifiable;

use App\Merchant;
use App\Http\Traits\CustomQuery;

class Merchant extends Model {
    use Notifiable, CustomQuery;

    protected $fillable = ['merchant_category_id', 'name', 'logo', 'status', 'floor', 'unit', 'business_reg_no', 'website', 'email', 'phone', 'description', 'terms_and_conditions', 'privacy_policy', 'created_by', 'updated_by'];
    protected $hidden = ['created_by', 'updated_by', 'created_at', 'updated_at'];
    protected $casts = [];

    // list of properties queryable for datatable
    public static $queryable = ['name', 'status', 'floor', 'unit', 'business_reg_no', 'website', 'email', 'phone'];

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    public function getLogoAttribute($value) {
        if ($value != null)
            return json_decode($value);
        
        return $value;
    }

    public function users() {
        return $this->belongsToMany(User::class, 'merchant_user', 'merchant_id', 'user_id'); 
    }

    public function category() {
        return $this->belongsTo(MerchantCategory::class);
    }

    public function account() {
        return $this->hasOne(MerchantAccount::class);
    }

    public function transactions() {
        return $this->hasMany(MerchantAccountTransaction::class);
    }
    
    public function visits() {
        return $this->hasMany(MerchantVisit::class);
    }

    public function vouchers() {
        return $this->hasMany(Voucher::class);
    }
}
