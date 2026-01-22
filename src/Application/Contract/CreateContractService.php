<?php

namespace App\Application\Contract;

use App\Application\DTO\CreateContractRequest;
use App\Application\DTO\CreateContractResponse;
use App\Domain\Repository\ContractRepositoryInterface;
use App\Infrastructure\Entity\Contract;

final class CreateContractService
{
    public function __construct(
        private ContractRepositoryInterface $repository
    ) {
    }

    public function execute(CreateContractRequest $request): CreateContractResponse
    {
        $existing = $this->repository->findByContractNumber($request->contractNumber);
        if ($existing !== null) {
            throw new \InvalidArgumentException('Contract number already exists');
        }

        $contract = new Contract(
            $request->contractNumber,
            $request->contractDate,
            $request->totalAmount,
            $request->paymentMethod
        );

        $this->repository->save($contract);

        return new CreateContractResponse(
            $contract->getId(),
            $contract->getContractNumber()
        );
    }
}
