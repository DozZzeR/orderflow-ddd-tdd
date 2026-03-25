<?php

namespace OrderFlow\Infrastructure\Adapters;

use OrderFlow\Application\Payment\PaymentSignatureVerifier;

class HmacPaymentSignatureVerifier implements PaymentSignatureVerifier
{
    public function __construct(private string $secret)
    {}

    public function isValid(string $payload, string $signature): bool
    {
        $expectedSignature  = hash_hmac('sha256', $payload, $this->secret);
        return hash_equals($expectedSignature , $signature);
    }
}