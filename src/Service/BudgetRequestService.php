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

    /** @var Category */
    private $category;

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
        string $address):BudgetRequest
    {
        $this->requiredFieldInformed($description);

        $this->userValidData($email, $phone, $address);

        $user = $this->userService->actualizeUser($email, $phone, $address);

        if (null != $categoryId)
        {
            $this->getCategoryById($categoryId);
        }

        $budgetRequest = new BudgetRequest($title, $description, $this->category, $user);

        $entityManager = $this->managerRegistry->getManagerForClass(BudgetRequest::class);
        $entityManager->persist($budgetRequest);
        $entityManager->flush();

        return $budgetRequest;
    }

    /**
     * @param int $categoryId
     * @throws Exception
     */
    public function getCategoryById(int $categoryId)
    {
        $this->category = $this->categoryRepository->findCategoryById($categoryId);

        if (null == $this->category)
        {
            throw new Exception('Category ' . $categoryId . ' does not exists', 100);
        }
    }
}