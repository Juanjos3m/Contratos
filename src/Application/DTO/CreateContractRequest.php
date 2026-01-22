<?php

namespace App\Application\DTO;

use App\Domain\Enum\PaymentMethod;

final class CreateContractRequest
{
    public function __construct(
        public readonly string $contractNumber,
        public readonly \DateTimeImmutable $contractDate,
        public readonly string $totalAmount,
        public readonly PaymentMethod $paymentMethod
    ) {}

    public static function fromArray(array $data): self
    {
        $contractNumber = trim((string) ($data['contractNumber'] ?? ''));
        $contractDateStr = trim((string) ($data['contractDate'] ?? ''));
        $totalAmount = trim((string) ($data['totalAmount'] ?? ''));
        $methodStr = trim((string) ($data['paymentMethod'] ?? 'PAYPAL'));

        if ($contractNumber === '' || $contractDateStr === '' || $totalAmount === '') {
            throw new \InvalidArgumentException('Missing required fields: contractNumber, contractDate, totalAmount');
        }

        try {
            $contractDate = new \DateTimeImmutable($contractDateStr);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Invalid contractDate format. Use YYYY-MM-DD');
        }

        try {
            $paymentMethod = PaymentMethod::from($methodStr);
        } catch (\ValueError $e) {
            throw new \InvalidArgumentException('Invalid paymentMethod. Use PAYPAL or PAYONLINE');
        }

        return new self($contractNumber, $contractDate, $totalAmount, $paymentMethod);
    }
}
