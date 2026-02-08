<?php

namespace App\Providers;

use App\Contracts\Repositories\PayableAccountRepositoryInterface;
use App\Repositories\PayableAccountRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            PayableAccountRepositoryInterface::class,
            PayableAccountRepository::class
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
