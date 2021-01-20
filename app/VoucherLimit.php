<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoucherLimit extends Model {

    protected $fillable = ['type', 'value'];
    protected $hidden = [];
    protected $casts = [];

    public $timestamps = false;

    const TYPE_PERSON = 'person';
    const TYPE_DAILY = 'daily';
    const TYPE_PERDAY = 'perday';
    const TYPE_TOTAL = 'total';

    const TYPES = [
        self::TYPE_PERSON,
        self::TYPE_DAILY,
        self::TYPE_PERDAY,
        self::TYPE_TOTAL,
    ];

    public function voucher() {
        return $this->belongsTo(Voucher::class);
    }
}
