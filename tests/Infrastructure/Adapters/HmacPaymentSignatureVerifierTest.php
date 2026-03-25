<?php

namespace Tests\Infrastructure\Adapters;

use OrderFlow\Infrastructure\Adapters\HmacPaymentSignatureVerifier;
use Tests\TestCase;

class HmacPaymentSignatureVerifierTest extends TestCase
{
    public function test_it_returns_true_when_hmac_matches(): void
    {
        $secret = 'secret';
        $payload = '{"key":"value"}';
        $signature = hash_hmac('sha256', $payload, $secret);

        $hmacVerifier = new HmacPaymentSignatureVerifier($secret);
        $this->assertTrue($hmacVerifier->isValid($payload, $signature));
    }

    public function test_it_returns_false_when_hmac_does_not_match(): void
    {
        $secret = 'secret';
        $payload = '{"key":"value"}';
        $wrongSecret = 'wrong-secret';
        $signature = hash_hmac('sha256', $payload, $wrongSecret);

        $hmacVerifier = new HmacPaymentSignatureVerifier($secret);
        $this->assertFalse($hmacVerifier->isValid($payload, $signature));
    }
}