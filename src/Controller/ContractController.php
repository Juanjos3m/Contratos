<?php

namespace App\Controller;

use App\Application\Contract\CreateContractService;
use App\Application\DTO\CreateContractRequest;
use App\Application\DTO\InstallmentProjectionResponse;
use App\Application\Installment\InstallmentProjectionService;
use App\Domain\Enum\PaymentMethod;
use App\Domain\Repository\ContractRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ContractController extends AbstractController
{
    #[Route('/contracts', methods: ['POST'])]
    public function create(
        Request $request,
        CreateContractService $createService
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            if (!is_array($data)) {
                return $this->json(['error' => 'Invalid JSON'], 400);
            }

            $createRequest = CreateContractRequest::fromArray($data);
            $response = $createService->execute($createRequest);

            return $this->json($response->toArray(), 201);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], 500);
        }
    }

    #[Route('/contracts/{id}/installments/projection', methods: ['GET'])]
    public function projection(
        int $id,
        Request $request,
        ContractRepositoryInterface $repository,
        InstallmentProjectionService $projectionService
    ): JsonResponse {
        try {
            $contract = $repository->findById($id);
            if (!$contract) {
                return $this->json(['error' => 'Contract not found'], 404);
            }

            $months = (int) $request->query->get('months', 1);
            if ($months <= 0) {
                return $this->json(['error' => 'months must be greater than 0'], 400);
            }

            $methodStr = (string) $request->query->get('method', $contract->getPaymentMethod()->value);

            try {
                $method = PaymentMethod::from($methodStr);
            } catch (\ValueError $e) {
                return $this->json(['error' => 'Invalid method. Use PAYPAL or PAYONLINE'], 400);
            }

            $installments = $projectionService->project($contract, $months, $method);

            $response = new InstallmentProjectionResponse(
                $contract->getId(),
                $months,
                $method->value,
                $installments
            );

            return $this->json($response->toArray());
        } catch (\Exception $e) {
            return $this->json(['error' => 'Internal server error'], 500);
        }
    }
}
