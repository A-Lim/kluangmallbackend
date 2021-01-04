<?php
namespace App;

use App\Merchant;
use App\Http\Traits\CustomQuery;
use Illuminate\Database\Eloquent\Model;

class MerchantAccountTransaction extends Model {
    use CustomQuery;

    protected $table = 'merchant_account_transactions';
    protected $fillable = ['merchant_id', 'title', 'type', 'credit', 'remark', 'refunded', 'refund_transaction_id', 'created_by'];
    protected $hidden = [];
    protected $casts = ['created_at' => 'datetime:d M Y H:i:s',];

    // list of properties queryable for datatable
    public static $queryable = ['type', 'credit'];

    const TYPE_TOPUP = 'topup';
    const TYPE_REFUND = 'refund';
    const TYPE_DEDUCT = 'deduct';
    const TYPE_RECREDIT = 'recredit';

    const TYPES = [
        self::TYPE_TOPUP,
        self::TYPE_REFUND,
        self::TYPE_DEDUCT,
        self::TYPE_RECREDIT
    ];

    public function merchant() {
        return $this->belongsTo(Merchant::class);
    }
}
