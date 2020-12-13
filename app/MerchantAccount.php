<?php
namespace App;

use App\Merchant;
use Illuminate\Database\Eloquent\Model;

class MerchantAccount extends Model {

    protected $table = 'merchant_accounts';
    protected $fillable = ['merchant_id', 'credit'];
    protected $hidden = [];
    protected $casts = [];

    const RATE = 1;

    public function merchant() {
        return $this->belongsTo(Merchant::class);
    }
}
