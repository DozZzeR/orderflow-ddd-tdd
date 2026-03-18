<?php

namespace Tests\Application\Payment;

use OrderFlow\Application\Order\CapturePaymentAction;
use OrderFlow\Application\Order\ProcessPaymentWebhookAction;
use OrderFlow\Application\Payment\InvalidPaymentSignature;
use OrderFlow\Application\Payment\PaymentEventStore;
use OrderFlow\Application\Payment\PaymentSignatureVerifier;
use OrderFlow\Domain\Order\OrderId;
use Tests\TestCase;

class PaymentEventStoreTest extends TestCase
{
    public function test_it_does_nothing_when_event_already_processed(): void
    {
        $orderId = OrderId::fromString('123');
        $eventId = "EventID";

        $paymentEventStore = $this->createMock(PaymentEventStore::class);
        $signatureVerifier = $this->createMock(PaymentSignatureVerifier::class);
        $capturePaymentAction = $this->createMock(CapturePaymentAction::class);

        $paymentEventStore->expects($this->once())->method('hasProcessed')->with($eventId)->willReturn(true);
        $signatureVerifier->expects($this->never())->method('isValid');
        $capturePaymentAction->expects($this->never())->method('handle');

        $useCase = new ProcessPaymentWebhookAction($paymentEventStore, $signatureVerifier, $capturePaymentAction);
        $useCase->handle($eventId, 'payload', 'signature', $orderId);
    }

    public function test_it_processes_new_valid_event(): void
    {
        $orderId = OrderId::fromString('123');
        $eventId = "EventID";

        $paymentEventStore = $this->createMock(PaymentEventStore::class);
        $signatureVerifier = $this->createMock(PaymentSignatureVerifier::class);
        $capturePaymentAction = $this->createMock(CapturePaymentAction::class);

        $paymentEventStore->expects($this->once())->method('hasProcessed')->with($eventId)->willReturn(false);
        $signatureVerifier->expects($this->once())->method('isValid')->with('payload','signature')->willReturn(true);
        $capturePaymentAction->expects($this->once())->method('handle')->with($orderId);

        $useCase = new ProcessPaymentWebhookAction($paymentEventStore, $signatureVerifier, $capturePaymentAction);
        $paymentEventStore->expects($this->once())->method('markAsProcessed')->with($eventId);
        $useCase->handle($eventId, 'payload', 'signature', $orderId);
    }

    public function test_it_throws_exception_when_signature_is_not_valid(): void
    {
        $orderId = OrderId::fromString('123');
        $eventId = 'eventId';

        $paymentEventStore = $this->createMock(PaymentEventStore::class);
        $signatureVerifier = $this->createMock(PaymentSignatureVerifier::class);
        $capturePaymentAction = $this->createMock(CapturePaymentAction::class);

        $paymentEventStore->expects($this->once())->method('hasProcessed')->with($eventId)->willReturn(false);
        $signatureVerifier->expects($this->once())->method('isValid')->with('payload', 'signature')->willReturn(false);
        $capturePaymentAction->expects($this->never())->method('handle');
        $paymentEventStore->expects($this->never())->method('markAsProcessed');

        $useCase = new ProcessPaymentWebhookAction($paymentEventStore, $signatureVerifier, $capturePaymentAction);
        $this->expectException(InvalidPaymentSignature::class);
        $useCase->handle($eventId, 'payload', 'signature', $orderId);
    }
}