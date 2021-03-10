<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Social extends Model {

    protected $fillable = ['social_id', 'social_type'];
    protected $hidden = [];
    protected $casts = [];

    public $timestamps = false;

    const STATUS_FACEBOOK = 'facebook';

    const STATUSES = [
        self::STATUS_FACEBOOK,
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
