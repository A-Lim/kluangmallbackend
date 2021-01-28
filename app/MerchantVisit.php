<?php
namespace App;

use App\Merchant;
use Illuminate\Database\Eloquent\Model;

class MerchantVisit extends Model {

    protected $table = 'merchant_visits';
    protected $fillable = ['device_id', 'merchant_id'];
    protected $hidden = [];
    protected $casts = [];

    public function merchant() {
        return $this->belongsTo(Merchant::class);
    }

    public function getLogoAttribute($value) {
        if ($value != null) 
            return json_decode($value);
        
        return $value;
    }
}
