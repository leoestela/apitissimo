<?php


namespace App\Api\Action\BudgetRequest;


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
    public function __invoke(int $budgetRequestId, Request $request):JsonResponse
    {
        $responseMessage = 'Solicitud de presupuesto descartada correctamente';
        $responseCode = 201;

        try
        {
            $budgetRequest = $this->budgetRequestService->getBudgetRequestById($budgetRequestId);

            if (null == $budgetRequest)
            {
                throw new Exception('Budget request ' . $budgetRequestId . ' not exists', 400);
            }

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

        return $this->getJsonResponse($this->transformResponseToArray($responseMessage, $responseCode), $responseCode);
    }
}