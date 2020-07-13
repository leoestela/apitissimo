<?php


namespace App\Api\Action\BudgetRequest;

use App\Message\Message;
use App\Api\EndpointUri;
use App\Api\RequestManager;
use App\Service\BudgetRequestService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class Create extends RequestManager
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

    /** @var int */
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
        $responseMessage = Message::BUDGET_REQUEST_CREATED_OK;
        $responseCode = JsonResponse::HTTP_CREATED;

        try
        {
            $jsonData = $this->getJsonData($request);

            if(null == $jsonData)
            {
                throw new Exception(Message::BUDGET_REQUEST_INVALID_JSON_FOR_CREATE, JsonResponse::HTTP_BAD_REQUEST);
            }

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

        return $this->getJsonResponse($this->transformResponseToArray($responseMessage, $responseCode), $responseCode);
    }

    /**
     * @param array $jsonData
     * @throws Exception
     */
    private function getPayload(array $jsonData)
    {
        $this->title = $this->getFieldData($jsonData, 'title',null);
        $this->description = $this->getFieldData($jsonData, 'description', null, true);
        $this->categoryId = $this->getFieldData($jsonData,'category_id', null);

        $userData = $this->getArrayInArrayData($jsonData, 'user_data', true);

        if(null != $userData)
        {
            $this->userEmail = $this->getFieldData($userData, 'email', null, true);
            $this->userPhone = $this->getFieldData($userData, 'phone', null, true);
            $this->userAddress = $this->getFieldData($userData, 'address', null, true);
        }

        $this->isNumericField($this->categoryId);
        $this->isNumericField($this->userPhone);
    }
}