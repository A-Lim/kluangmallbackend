<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use App\Http\Traits\CustomQuery;

class PointTransaction extends Model {
    use CustomQuery;

    protected $fillable = ['merchant_id', 'user_id', 'receipt_id', 'type', 'amount', 'description'];
    protected $hidden = ['updated_at', 'receipt_id'];
    protected $casts = [
        'created_at' => 'datetime:d M Y H:i a',
    ];

    public static $queryable = ['merchant_id', 'user_id', 'receipt_id', 'type', 'amount', 'description'];

    const TYPE_ADD = 'add';
    const TYPE_DEDUCT = 'deduct';
    const TYPE_PENDING = 'pending';

    const TYPES = [
        self::TYPE_ADD,
        self::TYPE_DEDUCT,
        self::TYPE_PENDING
    ];

    public function receipt() {
        return $this->belongsTo(Receipt::class);
    }
}
