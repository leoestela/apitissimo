<?php


namespace App\Api\Action\BudgetRequest;

use App\Api\EndpointUri;
use App\Service\BudgetRequestService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class Create
{
    /** @var BudgetRequestService */
    private $budgetRequestService;

    /** @var string */
    private $title;

    /** @var string */
    private $description;

    /** @var int */
    private $categoryId;

    /** @var string */
    private $userEmail;

    /** @var string */
    private $userPhone;

    /** @var string */
    private $userAddress;


    public function __construct(BudgetRequestService $budgetRequestService)
    {
        $this->budgetRequestService = $budgetRequestService;
    }

    /**
     * @Route(EndpointUri::URI_BUDGET_REQUEST, methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request):JsonResponse
    {
        $responseMessage = 'Solicitud de presupuesto creada';
        $responseCode = 201;

        try
        {
            $jsonData = $this->getJsonData($request);

            $this->getPayload($jsonData);

            $this->budgetRequestService->createBudgetRequest(
                $this->title,
                $this->description,
                $this->categoryId,
                $this->userEmail,
                $this->userPhone,
                $this->userAddress);
        }
        catch (Exception $exception)
        {
            $responseMessage = $exception->getMessage();
            $responseCode = $exception->getCode();
        }

        $response = new JsonResponse($responseMessage, $responseCode);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    private function getJsonData(Request $request)
    {
        $jsonData = json_decode($request->getContent(), true);

        if(null == $jsonData || JSON_ERROR_NONE != json_last_error())
        {
            throw new Exception('Invalid JSON body', 400);
        }

        return $jsonData;
    }

    /**
     * @param array $jsonData
     * @throws Exception
     */
    private function getPayload(array $jsonData)
    {
        $this->title = $this->getFieldData($jsonData, 'title');
        $this->description = $this->getFieldData($jsonData, 'description' ,true);
        $this->categoryId = $this->getFieldData($jsonData,'category_id');

        $userData = $this->getArrayInArrayData($jsonData, 'user_data');

        $this->userEmail = $this->getFieldData($userData, 'email',true);
        $this->userPhone = $this->getFieldData($userData, 'phone',true);
        $this->userAddress = $this->getFieldData($userData, 'address', true);
    }

    /**
     * @param array $arrayData
     * @param string $fieldName
     * @param bool $required
     * @return string|null
     * @throws Exception
     */
    private function getFieldData (array $arrayData, string $fieldName, bool $required = false):?string
    {
        $fieldData = null;

        if(array_key_exists($fieldName, $arrayData))
            $fieldData = $arrayData[$fieldName];
        elseif ($required)
        {
            throw new Exception ('Required field missing', 400);
        }

        return $fieldData;
    }

    /**
     * @param array $arrayData
     * @param string $arrayName
     * @return array
     * @throws Exception
     */
    private function getArrayInArrayData (array $arrayData, string $arrayName):array
    {
        $containedArray = null;

        if(array_key_exists($arrayName, $arrayData))
            $containedArray = $arrayData[$arrayName];
        else
        {
            throw new Exception ('Required field missing', 400);
        }

        return $containedArray;
    }
}