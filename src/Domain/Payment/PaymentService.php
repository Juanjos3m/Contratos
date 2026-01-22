<?php
namespace App\Domain\Payment;

interface PaymentService
{
    public function interestRate(): float; 
    public function feeRate(): float;      
    public function name(): string;
}
