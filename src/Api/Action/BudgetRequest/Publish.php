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

class Publish extends DataManager
{
    /** @var BudgetRequestRepository */
    private $budgetRequestRepository;

    /** @var BudgetRequestService */
    private $budgetRequestService;


    public function __construct(
        BudgetRequestRepository $budgetRequestRepository,
        BudgetRequestService $budgetRequestService)
    {
        $this->budgetRequestRepository = $budgetRequestRepository;
        $this->budgetRequestService = $budgetRequestService;
    }

    /** @Route(EndpointUri::URI_BUDGET_REQUEST_PUBLISH, methods={"PUT"})
     * @param int $budgetRequestId
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(int $budgetRequestId, Request $request):JsonResponse
    {
        $responseMessage = 'Solicitud de presupuesto publicada correctamente';
        $responseCode = 201;

        try
        {
            $budgetRequest = $this->getBudgetRequestById($budgetRequestId);

            if($budgetRequest->getStatus() != Status::STATUS_PENDING ||
                null == $budgetRequest->getTitle() ||
                null == $budgetRequest->getCategory()
            )
            {
                throw new Exception('Action not allowed', 400);
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

        $response = new JsonResponse($responseMessage, $responseCode);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @param int $budgetRequestId
     * @return BudgetRequest
     * @throws Exception
     */
    private function getBudgetRequestById(int $budgetRequestId): BudgetRequest
    {
        $budgetRequest = $this->budgetRequestRepository->findBudgetRequestById($budgetRequestId);

        if (null == $budgetRequest)
        {
            throw new Exception('Budget request ' . $budgetRequestId . ' not exists', 400);
        }

        return $budgetRequest;
    }
}