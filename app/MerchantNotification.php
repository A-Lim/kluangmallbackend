<?php
namespace App;

use App\Merchant;
use Illuminate\Database\Eloquent\Model;

class MerchantNotification extends Model {

    protected $table = 'merchant_notifications';
    protected $fillable = ['merchant_id', 'title', 'content', 'read'];
    protected $hidden = [];
    protected $casts = [];

    public function merchant() {
        return $this->belongsTo(Merchant::class);
    }
}
