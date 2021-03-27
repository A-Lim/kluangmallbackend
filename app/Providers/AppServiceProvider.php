<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        $this->app->resolving(LengthAwarePaginator::class, static function (LengthAwarePaginator $paginator) {
            return $paginator->appends(request()->query());
        });
        
        $this->app->resolving(Paginator::class, static function (Paginator $paginator) {
            return $paginator->appends(request()->query());
        });

        /**
         * Somehow PHP is not able to write in default /tmp directory and SwiftMailer was failing.
         * To overcome this situation, we set the TMPDIR environment variable to a new value.
         */
        // if (class_exists('Swift_Preferences')) {
        //     \Swift_Preferences::getInstance()->setTempDir(storage_path().'/tmp');
        // } else {
        //     \Log::warning('Class Swift_Preferences does not exists');
        // }
    }
}
