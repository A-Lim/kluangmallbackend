<?php
namespace App;

use App\Casts\DateFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class Receipt extends Model {

    protected $fillable = ['merchant_id', 'user_id', 'invoice_no', 'date', 'image', 'amount', 'points'];
    protected $hidden = [
        'created_at' => DateFormat::class,
        'updated_at' => DateFormat::class
    ];
    protected $casts = [
        'date' => DateFormat::class,
    ];

    public function merchant() {
        return $this->belongsTo(Merchant::class);
    }
}
