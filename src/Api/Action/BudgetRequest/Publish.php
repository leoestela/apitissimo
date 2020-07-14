<?php


namespace App\Api\Action\BudgetRequest;

use App\Api\EndpointUri;
use App\Api\RequestManager;
use App\Message\Message;
use App\Service\BudgetRequestService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class Publish extends RequestManager
{
    /** @var BudgetRequestService */
    private $budgetRequestService;


    public function __construct(BudgetRequestService $budgetRequestService)
    {
       $this->budgetRequestService = $budgetRequestService;
    }

    /** @Route(EndpointUri::URI_BUDGET_REQUEST_PUBLISH, methods={"PUT"})
     * @param int $budgetRequestId
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke($budgetRequestId, Request $request):JsonResponse
    {
        $responseMessage = Message::BUDGET_REQUEST_PUBLISHED_OK;
        $responseCode = JsonResponse::HTTP_OK;

        try
        {
            $budgetRequestIdIntValue = $this->valueToInteger($budgetRequestId);

            $budgetRequest = $this->budgetRequestService->getBudgetRequestById($budgetRequestIdIntValue);

            if (null == $budgetRequest)
            {
                throw new Exception(
                    Message::messageReplace('id', $budgetRequestIdIntValue, Message::BUDGET_REQUEST_ID_NOT_EXISTS),
                    JsonResponse::HTTP_BAD_REQUEST);
            }

            if($budgetRequest->getStatus() != Status::STATUS_PENDING ||
                null == $budgetRequest->getTitle() ||
                null == $budgetRequest->getCategory()
            )
            {
                throw new Exception(Message::BUDGET_REQUEST_PUBLISH_NOT_ALLOWED, JsonResponse::HTTP_BAD_REQUEST);
            }

            $categoryId = $budgetRequest->getCategory()->getId();

            $this->budgetRequestService->modifyBudgetRequest(
                $budgetRequest,
                $budgetRequest->getTitle(),
                $budgetRequest->getDescription(),
                $categoryId,
                Status::STATUS_PUBLISHED
            );
        }
        catch(Exception $exception)
        {
            $responseMessage = $exception->getMessage();
            $responseCode = $exception->getCode();
        }

        return $this->getJsonResponse($this->transformResponseToArray($responseMessage, $responseCode), $responseCode);
    }
}