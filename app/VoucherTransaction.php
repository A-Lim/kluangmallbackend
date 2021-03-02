<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\EnvTimezone;

class VoucherTransaction extends Model {
    use EnvTimezone;

    protected $fillable = ['myvoucher_id', 'merchant_id', 'user_id', 'voucher_id', 'type'];
    protected $hidden = [];
    protected $casts = [
        'created_at' => 'datetime:d M Y H:i a',
        'updated_at' => 'datetime:d M Y H:i a'
    ];

    const TYPE_REDEEM = 'redeem';
    const TYPE_USE = 'use';

    const TYPES = [
        self::TYPE_REDEEM,
        self::TYPE_USE,
    ];
}
