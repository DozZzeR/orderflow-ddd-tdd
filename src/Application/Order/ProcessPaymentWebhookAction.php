<?php

namespace OrderFlow\Application\Order;

use OrderFlow\Application\Order\CapturePaymentAction;
use OrderFlow\Application\Payment\InvalidPaymentSignature;
use OrderFlow\Application\Payment\PaymentEventStore;
use OrderFlow\Application\Payment\PaymentSignatureVerifier;
use OrderFlow\Domain\Order\OrderId;

final class ProcessPaymentWebhookAction
{
    public function __construct(
        private PaymentEventStore $paymentEventStore,
        private PaymentSignatureVerifier $signatureVerifier,
        private CapturePaymentAction $capturePaymentAction,
    ) {
    }

    public function handle(string $eventId, string $payload, string $signature, OrderId $orderId): void
    {
        if ($this->paymentEventStore->hasProcessed($eventId)) {
            return;
        }
        if (! $this->signatureVerifier->isValid($payload, $signature)) {
            throw new InvalidPaymentSignature();
        }

        $this->capturePaymentAction->handle($orderId);
        $this->paymentEventStore->markAsProcessed($eventId);
    }
}