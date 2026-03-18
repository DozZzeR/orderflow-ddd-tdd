<?php

namespace OrderFlow\Application\Order;

use OrderFlow\Application\Order\CapturePaymentAction;
use OrderFlow\Application\Payment\InvalidPaymentSignature;
use OrderFlow\Application\Payment\PaymentSignatureVerifier;
use OrderFlow\Domain\Order\OrderId;

final class ProcessPaymentWebhookAction
{
    public function __construct(
        private PaymentSignatureVerifier $signatureVerifier,
        private CapturePaymentAction $capturePaymentAction,
    ) {
    }

    public function handle(string $payload, string $signature, OrderId $orderId): void
    {
        if (! $this->signatureVerifier->isValid($payload, $signature)) {
            throw new InvalidPaymentSignature();
        }

        $this->capturePaymentAction->handle($orderId);
    }
}