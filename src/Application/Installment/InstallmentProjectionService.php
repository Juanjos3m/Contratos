<?php

namespace App\Application\Installment;

use App\Application\Payment\PaymentServiceFactory;
use App\Domain\Enum\PaymentMethod;
use App\Infrastructure\Entity\Contract;

final class InstallmentProjectionService
{
    public function __construct(private PaymentServiceFactory $factory) {}

    public function project(Contract $contract, int $months, PaymentMethod $method): array
    {
        if ($months <= 0) {
            throw new \InvalidArgumentException('months must be > 0');
        }

        $payment = $this->factory->get($method);

        $total = (float) $contract->getTotalAmount();
        $base = $total / $months;

        $rows = [];
        for ($i = 1; $i <= $months; $i++) {
            $saldoPendiente = $total - ($base * ($i - 1));
            $interest = $saldoPendiente * $payment->interestRate();

            $subtotal = $base + $interest;
            $fee = $subtotal * $payment->feeRate();

            $dueDate = $contract->getContractDate()->modify(sprintf('+%d month', $i));

            $rows[] = [
                'installment' => $i,
                'dueDate' => $dueDate->format('Y-m-d'),
                'base' => round($base, 2),
                'interest' => round($interest, 2),
                'fee' => round($fee, 2),
                'total' => round($subtotal + $fee, 2),
                'paymentMethod' => $method->value,
            ];
        }

        return $rows;
    }
}
