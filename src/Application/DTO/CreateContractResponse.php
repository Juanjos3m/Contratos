<?php

namespace App\Application\DTO;

final class CreateContractResponse
{
    public function __construct(
        public readonly int $id,
        public readonly string $contractNumber
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'contractNumber' => $this->contractNumber,
        ];
    }
}
