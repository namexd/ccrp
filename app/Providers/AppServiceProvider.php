<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::setLocale('zh');
        app('view')->prependNamespace('admin', resource_path('views/laravel-admin'));
        //
//        \DB::listen(function($query) {
//            $tmp = str_replace('?', '"'.'%s'.'"', $query->sql);
//            $tmp = vsprintf($tmp, $query->bindings);
//            $tmp = str_replace("\\","",$tmp);
//            \Log::info(' execution time: '.$query->time.'ms; '.$tmp."\n\n\t");
//
//        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        \API::error(function (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            abort(404);
        });
    }
}
