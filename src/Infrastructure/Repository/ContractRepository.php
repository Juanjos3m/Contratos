<?php

namespace App\Infrastructure\Repository;

use App\Domain\Repository\ContractRepositoryInterface;
use App\Infrastructure\Entity\Contract;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ContractRepository extends ServiceEntityRepository implements ContractRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contract::class);
    }

    public function findById(int $id): ?Contract
    {
        return parent::find($id);
    }

    public function save(Contract $contract): void
    {
        $em = $this->getEntityManager();
        $em->persist($contract);
        $em->flush();
    }

    public function findByContractNumber(string $contractNumber): ?Contract
    {
        return $this->findOneBy(['contractNumber' => $contractNumber]);
    }
}
