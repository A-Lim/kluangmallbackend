<?php
namespace App;

use Illuminate\Database\Eloquent\Model;


class Landing extends Model {

    protected $fillable = ['type', 'type_id', 'seq'];
    protected $hidden = [];
    protected $casts = [];

    const TYPE_EVENT = 'event';
    const TYPE_PROMOTION = 'promotion';

    const TYPES = [
        self::TYPE_EVENT,
        self::TYPE_PROMOTION,
    ];
}
