<?php


namespace App\Api\Action\BudgetRequest;


use App\Exception\BudgetRequest\BudgetRequestActionNotAllowedException;
use App\Exception\BudgetRequest\BudgetRequestNotExistsException;
use App\Exception\Common\InvalidJsonException;
use App\Message\Message;
use App\Api\EndpointUri;
use App\Api\RequestManager;
use App\Entity\BudgetRequest;
use App\Service\BudgetRequestService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class Modify extends RequestManager
{
    /** @var string */
    private $title;

    /** @var string */
    private $description;

    /** @var int */
    private $categoryId;

    /** @var string */
    private $status;

    /** @var BudgetRequestService */
    private $budgetRequestService;

    /** @var BudgetRequest */
    private $budgetRequest;


    public function __construct(BudgetRequestService $budgetRequestService)
    {
        $this->budgetRequestService = $budgetRequestService;
    }

    /** @Route(EndpointUri::URI_BUDGET_REQUEST_MODIFY, methods={"PUT"})
     * @param int $budgetRequestId
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke($budgetRequestId, Request $request):JsonResponse
    {
        $responseMessage = Message::BUDGET_REQUEST_MODIFIED_OK;
        $responseCode = JsonResponse::HTTP_OK;

        try
        {
            $this->getRequestInfo($budgetRequestId, $request);

            if($this->budgetRequest->getStatus() != Status::STATUS_PENDING)
            {
                throw BudgetRequestActionNotAllowedException::withAction('Modify');
            }

            $this->budgetRequestService->modifyBudgetRequest(
                $this->budgetRequest,
                $this->title,
                $this->description,
                $this->categoryId,
                $this->status
            );
        }
        catch (Exception $exception)
        {
            $responseMessage = $exception->getMessage();
            $responseCode = $exception->getCode();
        }

        return $this->getJsonResponse($this->transformResponseToArray($responseMessage, $responseCode), $responseCode);
    }

    /**
     * @param $budgetRequestId
     * @param Request $request
     * @throws BudgetRequestNotExistsException
     * @throws InvalidJsonException
     * @throws Exception
     */
    private function getRequestInfo($budgetRequestId, Request $request)
    {
        $budgetRequestIdIntValue = $this->valueToInteger($budgetRequestId);

        $jsonData = $this->getJsonData($request);

        if(null == $jsonData)
        {
            throw InvalidJsonException::throwException();
        }

        $this->budgetRequest = $this->budgetRequestService->getBudgetRequestById($budgetRequestIdIntValue);

        if (null == $this->budgetRequest)
        {
            throw BudgetRequestNotExistsException::withBudgetRequestId($budgetRequestId);
        }

        $this->getPayload($jsonData, $this->budgetRequest);
    }

    /**
     * @param array $arrayData
     * @param BudgetRequest $actualBudgetRequest
     * @throws Exception
     */
    private function getPayload(array $arrayData, BudgetRequest $actualBudgetRequest)
    {
        $this->title = $this->getFieldData($arrayData, 'title', $actualBudgetRequest->getTitle());

        $this->description = $this->getFieldData(
            $arrayData,
            'description',
            $actualBudgetRequest->getDescription(),
            true
        );

        $this->categoryId = $this->valueToInteger(
            $this->getFieldData($arrayData, 'category_id', $this->getCategoryId($actualBudgetRequest))
        );

        $this->status = $actualBudgetRequest->getStatus();
    }
}