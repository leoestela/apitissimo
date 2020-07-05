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
     * @return array
     * @throws Exception
     */
    private function getJsonData(Request $request): array
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
        $this->description = $this->getFieldData($jsonData, 'description');
        $this->categoryId = $this->getFieldData($jsonData,'category_id');

        $userData = $this->getArrayInArrayData($jsonData, 'user_data');

        if(null != $userData)
        {
            $this->userEmail = $this->getFieldData($userData, 'email');
            $this->userPhone = $this->getFieldData($userData, 'phone');
            $this->userAddress = $this->getFieldData($userData, 'address');
        }
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