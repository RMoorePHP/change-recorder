<?php

namespace RMoore\ChangeRecorder;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class ChangeRecorderServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

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
