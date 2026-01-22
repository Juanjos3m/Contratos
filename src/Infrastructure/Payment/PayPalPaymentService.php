<?php
namespace App\Infrastructure\Payment;

use App\Domain\Payment\PaymentService;

final class PayPalPaymentService implements PaymentService
{
    public function interestRate(): float { return 0.01; }
    public function feeRate(): float { return 0.02; }
    public function name(): string { return 'PAYPAL'; }
}
