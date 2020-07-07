<?php


namespace App\Api\Action\BudgetRequest;


use App\Entity\BudgetRequest;
use App\Repository\BudgetRequestRepository;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RequestManager
{
    /** @var BudgetRequestRepository*/
    private $budgetRequestRepository;


    public function __construct(BudgetRequestRepository $budgetRequestRepository)
    {
        $this->budgetRequestRepository = $budgetRequestRepository;
    }

    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    protected function getJsonData(Request $request): array
    {
        $jsonData = json_decode($request->getContent(), true);

        if(null == $jsonData || JSON_ERROR_NONE != json_last_error())
        {
            throw new Exception('Invalid JSON body', 400);
        }

        return $jsonData;
    }

    protected function getFieldData(array $arrayData, string $fieldName, ?string $defaultValue = null): ?string
    {
        return isset($arrayData[$fieldName]) ? $arrayData[$fieldName] : $defaultValue;
    }

    protected function getArrayInArrayData(array $arrayData, string $fieldName): ?array
    {
        return isset($arrayData[$fieldName]) ? $arrayData[$fieldName] : null;
    }

    /**
     * @param int $budgetRequestId
     * @return BudgetRequest
     * @throws Exception
     */
    protected function getBudgetRequestById(int $budgetRequestId): BudgetRequest
    {
        $budgetRequest = $this->budgetRequestRepository->findBudgetRequestById($budgetRequestId);

        if (null == $budgetRequest)
        {
            throw new Exception('Budget request ' . $budgetRequestId . ' not exists', 400);
        }

        return $budgetRequest;
    }

    protected function getCategoryId(BudgetRequest $budgetRequest): ?int
    {
        return (null != $budgetRequest->getCategory()) ? $budgetRequest->getCategory()->getId() : null;
    }

    protected function getJsonResponse(string $responseMessage, int $responseCode): JsonResponse
    {
        $response = new JsonResponse($responseMessage, $responseCode);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}