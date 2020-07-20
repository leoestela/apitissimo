<?php


namespace App\Api\Action\BudgetRequest;

use App\Api\EndpointUri;
use App\Api\RequestManager;
use App\Exception\BudgetRequest\BudgetRequestActionNotAllowedException;
use App\Exception\BudgetRequest\BudgetRequestNotExistsException;
use App\Message\Message;
use App\Service\BudgetRequestService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class Publish extends RequestManager
{
    /** @var BudgetRequestService */
    private $budgetRequestService;

    /** @var BudgetRequest */
    private $budgetRequest;


    public function __construct(BudgetRequestService $budgetRequestService)
    {
       $this->budgetRequestService = $budgetRequestService;
    }

    /** @Route(EndpointUri::URI_BUDGET_REQUEST_PUBLISH, methods={"PUT"})
     * @param int $budgetRequestId
     * @return JsonResponse
     */
    public function __invoke($budgetRequestId):JsonResponse
    {
        $responseMessage = Message::BUDGET_REQUEST_PUBLISHED_OK;
        $responseCode = JsonResponse::HTTP_OK;

        try
        {
            $this->getRequestInfo($budgetRequestId);

            //Can publish budget requests pending and with title and category informed only
            if($this->budgetRequest->getStatus() != Status::STATUS_PENDING ||
                null == $this->budgetRequest->getTitle() ||
                null == $this->budgetRequest->getCategory()
            )
            {
                throw BudgetRequestActionNotAllowedException::withAction('Publish');
            }

            $categoryId = $this->budgetRequest->getCategory()->getId();

            $this->budgetRequestService->modifyBudgetRequest(
                $this->budgetRequest,
                $this->budgetRequest->getTitle(),
                $this->budgetRequest->getDescription(),
                $categoryId,
                Status::STATUS_PUBLISHED
            );
        }
        catch(Exception $exception)
        {
            $responseMessage = $exception->getMessage();
            $responseCode = $exception->getCode();
        }

        return $this->formatResponseToJson($responseMessage, $responseCode);
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