<?php 

namespace App\Casts;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class DateFormat implements CastsAttributes {

    public function get($model, $key, $value, $attributes) {
        return Carbon::parse($value)
            ->setTimezone(env('APP_TIMEZONE', 'UTC'))
            ->format(env('APP_DATE_FORMAT'));
    }

    public function set($model, $key, $value, $attributes) {
        return $value;
    }
}