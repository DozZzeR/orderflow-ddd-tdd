<?php

namespace OrderFlow\Application\Payment;

interface PaymentSignatureVerifier
{
    public function isValid(string $payload, string $signature): bool;
}