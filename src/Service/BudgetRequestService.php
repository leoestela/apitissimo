<?php


namespace App\Service;

use App\Entity\BudgetRequest;
use App\Entity\Category;
use App\Exception\BudgetRequest\BudgetRequestNoChangesPassedException;
use App\Exception\Category\CategoryNotExistsException;
use App\Repository\BudgetRequestRepository;
use App\Repository\CategoryRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

class BudgetRequestService extends ValidationService
{
    /** @var UserService */
    private $userService;

    /** @var CategoryRepository */
    private $categoryRepository;

    /** @var ManagerRegistry */
    private $managerRegistry;

    /** @var BudgetRequestRepository */
    private $budgetRequestRepository;


    public function __construct(
        UserService $userService,
        CategoryRepository $categoryRepository,
        ManagerRegistry $managerRegistry,
        BudgetRequestRepository $budgetRequestRepository
    )
    {
        $this->userService = $userService;
        $this->categoryRepository = $categoryRepository;
        $this->managerRegistry = $managerRegistry;
        $this->budgetRequestRepository = $budgetRequestRepository;
    }

    /**
     * @param string|null $title
     * @param string $description
     * @param int|null $categoryId
     * @param string $email
     * @param int $phone
     * @param string $address
     * @return BudgetRequest
     * @throws Exception
     */
    public function createBudgetRequest(
        ?string $title,
        string $description,
        ?int $categoryId,
        string $email,
        int $phone,
        string $address): BudgetRequest
    {
        $user = $this->userService->actualizeUser($email, $phone, $address);

        $category = (null != $categoryId) ? $this->getCategoryById($categoryId) : null;

        $budgetRequest = new BudgetRequest($title, $description, $category, $user);

        $this->saveBudgetRequest($budgetRequest);

        return $budgetRequest;
    }

    /**
     * @param BudgetRequest $budgetRequest
     * @param string|null $title
     * @param string $description
     * @param int|null $categoryId
     * @param string $status
     * @return BudgetRequest
     * @throws Exception
     */
    public function modifyBudgetRequest(
        BudgetRequest $budgetRequest,
        ?string $title,
        string $description,
        ?int $categoryId,
        string $status): BudgetRequest
    {
        if($this->sameBudgetRequestData($budgetRequest, $title, $description, $categoryId, $status))
        {
            throw BudgetRequestNoChangesPassedException::throwException();
        }

        if(null != $categoryId && $categoryId != $this->getActualCategoryId($budgetRequest))
        {
            $budgetRequest->setCategory($this->getCategoryById($categoryId));
        }
        $budgetRequest->setTitle($title);
        $budgetRequest->setDescription($description);
        $budgetRequest->setStatus($status);

        $this->saveBudgetRequest($budgetRequest);

        return $budgetRequest;
    }
    
    public function getBudgetRequestById(int $budgetRequestId): ?BudgetRequest
    {
        return $this->budgetRequestRepository->findBudgetRequestById($budgetRequestId);
    }

    /**
     * @param int $categoryId
     * @return Category
     * @throws Exception
     */
    private function getCategoryById(int $categoryId): Category
    {
        $category = $this->categoryRepository->findCategoryById($categoryId);

        if (null == $category)
        {
            throw CategoryNotExistsException::withCategoryId($categoryId);
        }

        return $category;
    }

    /**
     * @param BudgetRequest $budgetRequest
     * @throws Exception
     */
    private function saveBudgetRequest(BudgetRequest $budgetRequest)
    {
        $this->constraintsValidation($budgetRequest);

        $entityManager = $this->managerRegistry->getManagerForClass(BudgetRequest::class);
        $entityManager->persist($budgetRequest);
        $entityManager->flush();
    }

    private function sameBudgetRequestData(
        BudgetRequest $actualBudgetRequest,
        ?string $title,
        string $description,
        ?int $categoryId,
        string $status): bool
    {
        return
            $title == $actualBudgetRequest->getTitle() &&
            $description == $actualBudgetRequest->getDescription() &&
            $categoryId == $this->getActualCategoryId($actualBudgetRequest) &&
            $status == $actualBudgetRequest->getStatus();
    }

    private function getActualCategoryId(BudgetRequest $actualBudgetRequest): ?int
    {
        return (null != $actualBudgetRequest->getCategory()) ? $actualBudgetRequest->getCategory()->getId() : null;
    }
}