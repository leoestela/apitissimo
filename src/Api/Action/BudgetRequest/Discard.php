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

class Discard extends RequestManager
{
    /** @var BudgetRequestService */
    private $budgetRequestService;


    public function __construct(
        BudgetRequestRepository $budgetRequestRepository,
        BudgetRequestService $budgetRequestService)
    {
        parent::__construct($budgetRequestRepository);

        $this->budgetRequestService = $budgetRequestService;
    }

    /** @Route(EndpointUri::URI_BUDGET_REQUEST_DISCARD, methods={"PUT"})
     * @param int $budgetRequestId
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(int $budgetRequestId, Request $request):JsonResponse
    {
        $responseMessage = 'Solicitud de presupuesto descartada correctamente';
        $responseCode = 201;

        try
        {
            $budgetRequest = $this->getBudgetRequestById($budgetRequestId);

            if($budgetRequest->getStatus() == Status::STATUS_DISCARDED)
            {
                throw new Exception('Action not allowed', 400);
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

        return $this->getJsonResponse($responseMessage, $responseCode);
    }
}