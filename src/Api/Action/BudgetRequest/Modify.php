<?php


namespace App\Api\Action\BudgetRequest;


use App\Api\EndpointUri;
use App\Entity\BudgetRequest;
use App\Repository\BudgetRequestRepository;
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


    public function __construct(
        BudgetRequestRepository $budgetRequestRepository,
        BudgetRequestService $budgetRequestService)
    {
        parent::__construct($budgetRequestRepository);

        $this->budgetRequestService = $budgetRequestService;
    }

    /** @Route(EndpointUri::URI_BUDGET_REQUEST_MODIFY, methods={"PUT"})
     * @param int $budgetRequestId
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(int $budgetRequestId, Request $request):JsonResponse
    {
        $responseMessage = 'Solicitud de presupuesto modificada correctamente';
        $responseCode = 201;

        try
        {
            $jsonData = $this->getJsonData($request);

            $budgetRequest = $this->getBudgetRequestById($budgetRequestId);

            $this->getPayload($jsonData, $budgetRequest);

            if($this->status != Status::STATUS_PENDING || $budgetRequest->getStatus() != Status::STATUS_PENDING)
            {
                throw new Exception('Action not allowed', 400);
            }

            $this->budgetRequestService->modifyBudgetRequest(
                $budgetRequest,
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

        return $this->getJsonResponse($responseMessage, $responseCode);
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
            $actualBudgetRequest->getDescription()
        );
        $this->categoryId =$this->getFieldData(
            $arrayData,
            'category_id',
            $this->getCategoryId($actualBudgetRequest)
        );

        $this->status = $this->getFieldData($arrayData, 'status', $actualBudgetRequest->getStatus());
    }
}