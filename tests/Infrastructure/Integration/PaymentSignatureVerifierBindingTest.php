<?php

namespace Tests\Infrastructure\Integration;

use Laravel\SerializableClosure\Signers\Hmac;
use OrderFlow\Application\Payment\PaymentSignatureVerifier;
use OrderFlow\Infrastructure\Adapters\HmacPaymentSignatureVerifier;
use Tests\TestCase;

class PaymentSignatureVerifierBindingTest extends TestCase
{
    public function test_it_resolves_payment_signature_verifier_from_container(): void
    {
        $verifier = $this->app->make(PaymentSignatureVerifier::class);
        $this->assertInstanceOf(HmacPaymentSignatureVerifier::class, $verifier);
    }
    
    public function test_it_uses_secret_from_config_when_resolved_from_container(): void
    {
        config(['services.payment.secret' => 'test_secret']);
        $payload = '{"key": "value"}';
        $signature = hash_hmac('sha256', $payload, 'test_secret');
        $verifier = $this->app->make(PaymentSignatureVerifier::class);
        $this->assertTrue($verifier->isValid($payload, $signature));
    }
}
