<?php
namespace App;

use App\Merchant;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\EnvTimezone;

class MerchantAccount extends Model {
    use EnvTimezone;

    protected $table = 'merchant_accounts';
    protected $fillable = ['merchant_id', 'credit'];
    protected $hidden = [];
    protected $casts = [
        'created_at' => 'datetime:d M Y H:i a',
        'updated_at' => 'datetime:d M Y H:i a',
    ];

    public function merchant() {
        return $this->belongsTo(Merchant::class);
    }
}
