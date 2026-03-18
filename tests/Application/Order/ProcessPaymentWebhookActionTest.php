<?php

namespace Tests\Application\Order;

use OrderFlow\Application\Order\CapturePaymentAction;
use OrderFlow\Application\Order\ProcessPaymentWebhookAction;
use OrderFlow\Application\Payment\InvalidPaymentSignature;
use OrderFlow\Application\Payment\PaymentEventStore;
use OrderFlow\Application\Payment\PaymentSignatureVerifier;
use OrderFlow\Domain\Order\OrderId;
use Tests\TestCase;

class ProcessPaymentWebhookActionTest extends TestCase
{
    public function test_it_calls_capture_payment_when_signature_is_valid(): void
    {
        $orderId = OrderId::fromString('123');
        $paymentEventStore = $this->createMock(PaymentEventStore::class);
        $signatureVerifier = $this->createMock(PaymentSignatureVerifier::class);
        $capturePaymentAction = $this->createMock(CapturePaymentAction::class);

        $paymentEventStore->expects($this->once())->method('hasProcessed')->with('eventId')->willReturn(false);
        $signatureVerifier->expects($this->once())->method('isValid')->with('payload', 'signature')->willReturn(true);
        $capturePaymentAction->expects($this->once())->method('handle')->with($orderId);

        $useCase = new ProcessPaymentWebhookAction($paymentEventStore, $signatureVerifier, $capturePaymentAction);
        $useCase->handle('eventId', 'payload', 'signature', $orderId);
    }

    public function test_it_throws_exception_when_signature_is_not_valid(): void
    {
        $orderId = OrderId::fromString('123');

        $paymentEventStore = $this->createMock(PaymentEventStore::class);
        $signatureVerifier = $this->createMock(PaymentSignatureVerifier::class);
        $capturePaymentAction = $this->createMock(CapturePaymentAction::class);

        $paymentEventStore->expects($this->once())->method('hasProcessed')->with('eventId')->willReturn(false);
        $signatureVerifier->expects($this->once())->method('isValid')->with('payload', 'signature')->willReturn(false);
        $capturePaymentAction->expects($this->never())->method('handle');

        $useCase = new ProcessPaymentWebhookAction($paymentEventStore, $signatureVerifier, $capturePaymentAction);
        $this->expectException(InvalidPaymentSignature::class);
        $useCase->handle('eventId', 'payload', 'signature', $orderId);
    }
}
