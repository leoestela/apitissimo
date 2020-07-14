<?php


namespace App\Api\Action\BudgetRequest;


use App\Exception\BudgetRequest\BudgetRequestActionNotAllowedException;
use App\Exception\BudgetRequest\BudgetRequestNotExistsException;
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
            $budgetRequestIdIntValue = $this->valueToInteger($budgetRequestId);

            $budgetRequest = $this->budgetRequestService->getBudgetRequestById($budgetRequestIdIntValue);

            if (null == $budgetRequest)
            {
                throw BudgetRequestNotExistsException::withBudgetRequestId($budgetRequestId);
            }

            if($budgetRequest->getStatus() == Status::STATUS_DISCARDED)
            {
                throw BudgetRequestActionNotAllowedException::withAction('Discard');
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