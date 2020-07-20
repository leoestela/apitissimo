<?php


namespace App\Api;


use App\Entity\BudgetRequest;
use App\Exception\Common\InvalidJsonException;
use App\Exception\Common\RequiredFieldMissingException;
use App\Exception\Common\InvalidIdFormatException;
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
            throw InvalidJsonException::throwException();
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
        //Will load field value or default value passed
        $fieldData = isset($arrayData[$fieldName]) ? $arrayData[$fieldName] : $defaultValue;

        if($required && null == $fieldData)
        {
            throw RequiredFieldMissingException::throwException();
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
        //Will load array searched or null
        $array = isset($arrayData[$fieldName]) ? $arrayData[$fieldName] : null;

        if($required && null == $array)
        {
            throw RequiredFieldMissingException::throwException();
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

        //The value will be valid if is numeric and the conversion from int to string is equal to the original string
        if(null != $valuePassed && (!is_numeric($valuePassed) || $valuePassed != strval($intValue) || $intValue < 0))
        {
            throw InvalidIdFormatException::withValue($valuePassed);
        }

        return $intValue;
    }

    protected function formatResponseToJson($responseContent, int $responseCode): JsonResponse
    {
        if(is_array($responseContent))
        {
            $responseContentAsArray = $responseContent;
        }
        else
            {
                $responseContentAsArray = array ('message' => $responseContent, 'code' => $responseCode);
            }
        $response = new JsonResponse($responseContentAsArray, $responseCode);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}