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

class Discard extends RequestManager
{
    /** @var BudgetRequestService */
    private $budgetRequestService;


    public function __construct(BudgetRequestService $budgetRequestService)
    {
        $this->budgetRequestService = $budgetRequestService;
    }

    /** @Route(EndpointUri::URI_BUDGET_REQUEST_DISCARD, methods={"PUT"})
     * @param int $budgetRequestId
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke($budgetRequestId, Request $request):JsonResponse
    {
        $responseMessage = Message::BUDGET_REQUEST_DISCARD_OK;
        $responseCode = JsonResponse::HTTP_OK;

        try
        {
            $this->isNumericField($budgetRequestId);

            $budgetRequest = $this->budgetRequestService->getBudgetRequestById($budgetRequestId);

            if (null == $budgetRequest)
            {
                throw new Exception(
                    Message::messageReplace('id', $budgetRequestId, Message::BUDGET_REQUEST_ID_NOT_EXISTS),
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }

            if($budgetRequest->getStatus() == Status::STATUS_DISCARDED)
            {
                throw new Exception(Message::BUDGET_REQUEST_DISCARD_NOT_ALLOWED, JsonResponse::HTTP_METHOD_NOT_ALLOWED);
            }

            $this->budgetRequestService->modifyBudgetRequest(
                $budgetRequest,
                $budgetRequest->getTitle(),
                $budgetRequest->getDescription(),
                $this->getCategoryId($budgetRequest),
                Status::STATUS_DISCARDED
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