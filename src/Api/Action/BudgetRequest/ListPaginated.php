<?php


namespace App\Api\Action\BudgetRequest;


use App\Api\EndpointUri;
use App\Api\RequestManager;
use App\Entity\User;
use App\Exception\User\UserNotExistsException;
use App\Repository\BudgetRequestRepository;
use App\Service\UserService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ListPaginated extends RequestManager
{
    /** @var UserService */
    private $userService;

    /** @var BudgetRequestRepository */
    private $budgetRequestRepository;

    /** @var User */
    private $user;

    /** @var string */
    private $email;

    /** @var int */
    private $limit;

    /** @var int */
    private $offset;


    public function __construct(UserService $userService, BudgetRequestRepository $budgetRequestRepository)
    {
        $this->userService = $userService;
        $this->budgetRequestRepository = $budgetRequestRepository;
    }

    /**
     * @Route(EndpointUri::URI_BUDGET_REQUEST, methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request):JsonResponse
    {
        $jsonContent = [];
        $responseMessage = '';
        $responseCode = JsonResponse::HTTP_OK;

        try
        {
            $this->getRequestInfo($request);

            //Find all or find by user
            $criteria = (null != $this->user) ? ['user' => $this->user->getId()] : [];

            $budgetRepositoryCollection =
                $this->budgetRequestRepository->findByWithPagination($criteria, null, $this->limit, $this->offset);

            if(null != $budgetRepositoryCollection)
            {
                $jsonContent = $this->serializeBudgetRequestCollection($budgetRepositoryCollection);
            }
        }
        catch (Exception $exception)
        {
            $responseMessage = $exception->getMessage();
            $responseCode = $exception->getCode();
        }

        //Response can be a list of budget request or an error message
        $responseContent = (null == $responseMessage) ? $jsonContent : $responseMessage;

        return $this->formatResponseToJson($responseContent, $responseCode);
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    private function getRequestInfo(Request $request)
    {
        $jsonData = $this->getJsonData($request);

        if(null != $jsonData)
        {
            $this->getPayload($jsonData);

            $this->getUserByEmail($this->email);
        }
    }

    /**
     * @param array $jsonData
     * @throws Exception
     */
    private function getPayload(array $jsonData)
    {
        $this->email = (null != $jsonData) ? $this->getFieldData($jsonData, 'email', null) : null;
        $this->limit = (null != $jsonData) ? $this->getFieldData($jsonData, 'limit', null) : null;
        $this->offset = (null != $jsonData) ? $this->getFieldData($jsonData, 'offset', null) : null;
    }

    /**
     * @param string $email
     * @throws Exception
     */
    private function getUserByEmail(string $email)
    {
       $this->user = (null != $email) ? $this->userService->getUserByEmail($this->email) : null;

        if(null != $email && null == $this->user)
        {
            throw UserNotExistsException::withUserEmail($email);
        }
    }

    private function serializeBudgetRequestCollection (array $budgetRequestCollection): array
    {
        $data = array('budget_requests' => array());

        foreach ($budgetRequestCollection as $budgetRequest) {
            $data['budget_requests'][] = $budgetRequest->serialize();
        }

        return $data;
    }
}