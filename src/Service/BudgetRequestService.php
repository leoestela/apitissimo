<?php


namespace App\Service;

use App\Entity\BudgetRequest;
use App\Entity\Category;
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


    public function __construct(
        UserService $userService,
        CategoryRepository $categoryRepository,
        ManagerRegistry $managerRegistry)
    {
        $this->userService = $userService;
        $this->categoryRepository = $categoryRepository;
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @param string|null $title
     * @param string $description
     * @param int|null $categoryId
     * @param string $email
     * @param string $phone
     * @param string $address
     * @return BudgetRequest
     * @throws Exception
     */
    public function createBudgetRequest(
        ?string $title,
        string $description,
        ?int $categoryId,
        string $email,
        string $phone,
        string $address): BudgetRequest
    {
        $this->requiredFieldInformed($description);

        $this->userValidData($email, $phone, $address);

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
     * @return BudgetRequest
     * @throws Exception
     */
    public function modifyBudgetRequest(
        BudgetRequest $budgetRequest,
        ?string $title,
        string $description,
        ?int $categoryId): BudgetRequest
    {
        $this->requiredFieldInformed($description);

        if($this->sameBudgetRequestInfo($budgetRequest, $title, $description, $categoryId))
        {
            throw new Exception('No changes made', 400);
        }

        if(null != $categoryId && $categoryId != $this->getActualCategoryId($budgetRequest))
        {
            $budgetRequest->setCategory($this->getCategoryById($categoryId));
        }
        $budgetRequest->setTitle($title);
        $budgetRequest->setDescription($description);

        $this->saveBudgetRequest($budgetRequest);

        return $budgetRequest;
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
            throw new Exception('Category ' . $categoryId . ' does not exists', 400);
        }

        return $category;
    }

    private function saveBudgetRequest(BudgetRequest $budgetRequest)
    {
        $entityManager = $this->managerRegistry->getManagerForClass(BudgetRequest::class);
        $entityManager->persist($budgetRequest);
        $entityManager->flush();
    }

    private function sameBudgetRequestInfo(
        BudgetRequest $actualBudgetRequest,
        ?string $title,
        string $description,
        ?int $categoryId): bool
    {
        return
            $title == $actualBudgetRequest->getTitle() &&
            $description == $actualBudgetRequest->getDescription() &&
            $categoryId == $this->getActualCategoryId($actualBudgetRequest);
    }

    private function getActualCategoryId(BudgetRequest $actualBudgetRequest): ?int
    {
        return (null != $actualBudgetRequest->getCategory()) ? $actualBudgetRequest->getCategory()->getId() : null;
    }
}