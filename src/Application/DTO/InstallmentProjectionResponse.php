<?php

namespace App\Application\DTO;

final class InstallmentProjectionResponse
{
    public function __construct(
        public readonly int $contractId,
        public readonly int $months,
        public readonly string $method,
        public readonly array $installments
    ) {}

    public function toArray(): array
    {
        return [
            'contractId' => $this->contractId,
            'months' => $this->months,
            'method' => $this->method,
            'installments' => $this->installments,
        ];
    }
}
