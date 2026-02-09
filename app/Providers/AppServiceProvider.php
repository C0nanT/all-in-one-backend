<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\PayableAccount\Contracts\Repositories\PayableAccountPaymentRepositoryInterface;
use Modules\PayableAccount\Contracts\Repositories\PayableAccountRepositoryInterface;
use Modules\PayableAccount\Repositories\PayableAccountPaymentRepository;
use Modules\PayableAccount\Repositories\PayableAccountRepository;

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
        $this->app->bind(
            PayableAccountPaymentRepositoryInterface::class,
            PayableAccountPaymentRepository::class
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
