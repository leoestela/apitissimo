<?php


namespace App\Api\Action\BudgetRequest;


use App\Entity\BudgetRequest;
use App\Exception\BudgetRequest\BudgetRequestActionNotAllowedException;
use App\Exception\BudgetRequest\BudgetRequestNotExistsException;
use App\Message\Message;
use App\Api\EndpointUri;
use App\Api\RequestManager;
use App\Service\BudgetRequestService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class Discard extends RequestManager
{
    /** @var BudgetRequestService */
    private $budgetRequestService;

    /** @var BudgetRequest */
    private $budgetRequest;


    public function __construct(BudgetRequestService $budgetRequestService)
    {
        $this->budgetRequestService = $budgetRequestService;
    }

    /** @Route(EndpointUri::URI_BUDGET_REQUEST_DISCARD, methods={"PUT"})
     * @param int $budgetRequestId
     * @return JsonResponse
     */
    public function __invoke($budgetRequestId):JsonResponse
    {
        $responseMessage = Message::BUDGET_REQUEST_DISCARDED_OK;
        $responseCode = JsonResponse::HTTP_OK;

        try
        {
            $this->getRequestInfo($budgetRequestId);

            if($this->budgetRequest->getStatus() == Status::STATUS_DISCARDED)
            {
                throw BudgetRequestActionNotAllowedException::withAction('Discard');
            }

            $this->budgetRequestService->modifyBudgetRequest(
                $this->budgetRequest,
                $this->budgetRequest->getTitle(),
                $this->budgetRequest->getDescription(),
                $this->getCategoryId($this->budgetRequest),
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

    /**
     * @param $budgetRequestId
     * @throws BudgetRequestNotExistsException
     * @throws Exception
     */
    private function getRequestInfo($budgetRequestId)
    {
        $budgetRequestIdIntValue = $this->valueToInteger($budgetRequestId);

        $this->budgetRequest = $this->budgetRequestService->getBudgetRequestById($budgetRequestIdIntValue);

        if (null == $this->budgetRequest)
        {
            throw BudgetRequestNotExistsException::withBudgetRequestId($budgetRequestId);
        }
    }
}