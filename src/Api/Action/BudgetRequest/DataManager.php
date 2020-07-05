<?php


namespace App\Api\Action\BudgetRequest;


use Exception;
use Symfony\Component\HttpFoundation\Request;

class DataManager
{
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
}