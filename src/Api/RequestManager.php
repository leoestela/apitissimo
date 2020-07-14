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

    /**
     * @param array $arrayData
     * @param string $fieldName
     * @param string|null $defaultValue
     * @param bool $required
     * @return string|null
     * @throws Exception
     */
    protected function getFieldData(
        array $arrayData,
        string $fieldName,
        ?string $defaultValue,
        bool $required = false): ?string
    {
        $fieldData = isset($arrayData[$fieldName]) ? $arrayData[$fieldName] : $defaultValue;

        if($required && null == $fieldData)
        {
            throw new Exception('Required field missing', JsonResponse::HTTP_BAD_REQUEST);
        }

        return $fieldData;
    }

    /**
     * @param array $arrayData
     * @param string $fieldName
     * @param bool $required
     * @return array|null
     * @throws Exception
     */
    protected function getArrayInArrayData(array $arrayData, string $fieldName, bool $required = false): ?array
    {
        $array = isset($arrayData[$fieldName]) ? $arrayData[$fieldName] : null;

        if($required && null == $array)
        {
            throw new Exception('Required field missing', JsonResponse::HTTP_BAD_REQUEST);
        }

        return $array;
    }

    protected function getCategoryId(BudgetRequest $budgetRequest): ?int
    {
        return (null != $budgetRequest->getCategory()) ? $budgetRequest->getCategory()->getId() : null;
    }

    /**
     * @param $valuePassed
     * @return int
     * @throws Exception
     */
    protected function valueToInteger($valuePassed): ?int
    {
        $intValue = (null != $valuePassed) ? intval($valuePassed) : null;

        if(null != $valuePassed && (!is_numeric($valuePassed) || $valuePassed != strval($intValue) || $intValue < 0))
        {
            throw new Exception ('Field must be a valid positive integer', JsonResponse::HTTP_BAD_REQUEST);
        }

        return $intValue;
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