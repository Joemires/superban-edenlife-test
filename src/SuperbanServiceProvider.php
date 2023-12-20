<?php

declare(strict_types=1);

namespace Joemires\Superban;

use Illuminate\Support\ServiceProvider;

final class SuperbanServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config/superban.php' => config_path('superban.php')], 'config');
        }

        app('router')->aliasMiddleware('superban', Middleware\Superban::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/superban.php', 'superban');
    }
}
