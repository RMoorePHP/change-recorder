<?php

namespace RMoore\ChangeRecorder;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class ChangeRecorderServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'migrations');

        Router::macro('history', function ($name, $class) {
            $this->get($name.'/history', function () use ($class) {
                return Change::where('subject_type', $class);
            });
            $this->get("$name/{key}/history", function ($key) use ($class) {
                $field = (new $class)->getRouteKeyName() ?: 'id';

                return $class::where($field, $key)->firstOrFail()->getHistory();
            });
            $this->get("$name/{key}/collaborators", function ($key) use ($class) {
                $field = (new $class)->getRouteKeyName() ?: 'id';

                return $class::where($field, $key)->firstOrFail()->collaborators;
            });
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
