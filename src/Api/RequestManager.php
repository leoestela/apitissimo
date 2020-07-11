<?php


namespace App\Api;


use App\Entity\BudgetRequest;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RequestManager
{
    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    protected function getJsonData(Request $request): ?array
    {
        $jsonData = json_decode($request->getContent(), true);

        if(null != $request->getContent() && JSON_ERROR_NONE != json_last_error())
        {
            throw new Exception('Invalid JSON body', JsonResponse::HTTP_BAD_REQUEST);
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

    protected function getCategoryId(BudgetRequest $budgetRequest): ?int
    {
        return (null != $budgetRequest->getCategory()) ? $budgetRequest->getCategory()->getId() : null;
    }

    protected function transformResponseToArray(string $message, int $code): array
    {
        return array('message' => $message, 'code' => $code);
    }

    protected function getJsonForEmptyData(int $code): array
    {
        return array('message' => 'No results found', 'code' => $code);
    }

    protected function getJsonResponse(array $responseContent, int $responseCode): JsonResponse
    {
        $response = new JsonResponse($responseContent, $responseCode);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}