<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
        \App\Interfaces\ProductRepositoryInterface::class,
        \App\Repositories\ProductRepository::class
    );
   $this->app->bind(
    \App\Interfaces\CartServiceInterface::class,
    \App\Services\CartService::class
);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
