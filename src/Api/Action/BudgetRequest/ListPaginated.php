<?php


namespace App\Api\Action\BudgetRequest;

use App\Api\EndpointUri;
use App\Api\RequestManager;
use App\Api\Serializer;
use App\Entity\User;
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

    /** @var Serializer */
    private $serializer;


    public function __construct(
        UserService $userService,
        BudgetRequestRepository $budgetRequestRepository,
        Serializer $serializer)
    {
        $this->userService = $userService;
        $this->budgetRequestRepository = $budgetRequestRepository;
        $this->serializer = $serializer;
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
        $responseCode = 200;

        try
        {
            $jsonData = $this->getJsonData($request);

            if(null != $jsonData)
            {
                $this->getPayload($jsonData);

                $this->getUserByEmail($this->email);
            }

            $criteria = (null != $this->user) ? ['user_id' => $this->user->getId()] : [];

            $budgetRepositoryCollection =
                $this->budgetRequestRepository->findByWithPagination($criteria, null, $this->limit, $this->offset);

            if(null != $budgetRepositoryCollection)
            {
                $jsonContent = $this->serializer->serializeBudgetRequestCollection($budgetRepositoryCollection);
            }
        }
        catch (Exception $exception)
        {
            $responseMessage = $exception->getMessage();
            $responseCode = $exception->getCode();
        }

        $responseContent = (null == $responseMessage)
            ? $jsonContent : $this->transformResponseToArray($responseMessage, $responseCode);

        $responseContent = (null == $responseContent) ? $this->getJsonForEmptyData($responseCode) : $responseContent;

        return $this->getJsonResponse($responseContent, $responseCode);
    }

    private function getPayload(array $jsonData)
    {
        $this->email = (null != $jsonData) ? $this->getFieldData($jsonData, 'email', null) : null;
        $this->limit = (null != $jsonData) ? $this->getFieldData($jsonData, 'limit', null) : null;
        $this->offset = (null != $jsonData) ? $this->getFieldData($jsonData, 'offset', null) : null;
    }

    /**
     * @param string $email
     * @return User
     * @throws Exception
     */
    private function getUserByEmail(string $email)
    {
       $this->user = (null != $email) ? $this->userService->getUserByEmail($this->email) : null;

        if(null != $email && null == $this->user)
        {
            throw new Exception('User ' . 'not exists', 400);
        }
    }
}