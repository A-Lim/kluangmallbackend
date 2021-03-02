<?php
namespace App;

use App\Merchant;
use App\Casts\DateFormat;
use Illuminate\Database\Eloquent\Model;

class MerchantAccount extends Model {

    protected $table = 'merchant_accounts';
    protected $fillable = ['merchant_id', 'credit'];
    protected $hidden = [];
    protected $casts = [
        'created_at' => DateFormat::class,
        'updated_at' => DateFormat::class,
    ];

    public function merchant() {
        return $this->belongsTo(Merchant::class);
    }
}
