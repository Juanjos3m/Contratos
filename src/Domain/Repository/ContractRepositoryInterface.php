<?php

namespace App\Domain\Repository;

use App\Infrastructure\Entity\Contract;

interface ContractRepositoryInterface
{

    public function findById(int $id): ?Contract;
    
    public function save(Contract $contract): void;

    public function findByContractNumber(string $contractNumber): ?Contract;
}
