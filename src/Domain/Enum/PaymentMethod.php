<?php
namespace App\Domain\Enum;

enum PaymentMethod: string
{
    case PAYPAL = 'PAYPAL';
    case PAYONLINE = 'PAYONLINE';
}
