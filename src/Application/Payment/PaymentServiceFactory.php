<?php
namespace App\Application\Payment;

use App\Domain\Enum\PaymentMethod;
use App\Domain\Payment\PaymentService;
use App\Infrastructure\Payment\PayOnlinePaymentService;
use App\Infrastructure\Payment\PayPalPaymentService;

final class PaymentServiceFactory
{
    public function __construct(
        private PayPalPaymentService $paypal,
        private PayOnlinePaymentService $payOnline
    ) {}

    public function get(PaymentMethod $method): PaymentService
    {
        return match ($method) {
            PaymentMethod::PAYPAL => $this->paypal,
            PaymentMethod::PAYONLINE => $this->payOnline,
        };
    }
}
