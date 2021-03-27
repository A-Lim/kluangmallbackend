<?php
namespace App\Http\Traits;

use Carbon\Carbon;
use DateTimeInterface;

trait EnvTimezone {

    protected function serializeDate(DateTimeInterface $date) {
        return Carbon::instance($date)
            ->setTimezone(env('APP_TIMEZONE', 'UTC'));
    
    }
}