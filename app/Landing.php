<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Landing extends Model {

    protected $fillable = ['type', 'type_id', 'seq'];
    protected $hidden = [];
    protected $casts = [];

    const APP_USER = 'user';
    const APP_MERCHANT = 'merchant';

    const TYPE_BANNER = 'banner';
    const TYPE_EVENT = 'event';
    const TYPE_PROMOTION = 'promotion';

    const TYPES = [
        self::TYPE_BANNER,
        self::TYPE_EVENT,
        self::TYPE_PROMOTION,
    ];

    const APPS = [
        self::APP_USER,
        self::APP_MERCHANT,
    ];
}
