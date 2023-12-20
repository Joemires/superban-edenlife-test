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
        app('router')->aliasMiddleware('superban', Middleware\Superban::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
