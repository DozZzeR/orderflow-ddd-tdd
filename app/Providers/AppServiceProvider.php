<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OrderFlow\Application\Payment\PaymentSignatureVerifier;
use OrderFlow\Infrastructure\Adapters\HmacPaymentSignatureVerifier;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(PaymentSignatureVerifier::class, function() {
            $secret = config('services.payment.secret');
            return new HmacPaymentSignatureVerifier($secret);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
