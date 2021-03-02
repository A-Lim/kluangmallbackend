<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use App\Http\Traits\EnvTimezone;

class Receipt extends Model {
    use EnvTimezone;

    protected $fillable = ['merchant_id', 'user_id', 'invoice_no', 'date', 'image', 'amount', 'points'];
    protected $hidden = [];
    protected $casts = [
        'date' => 'datetime:d M Y',
    ];

    public function merchant() {
        return $this->belongsTo(Merchant::class);
    }
}
