<?php


namespace App\Service;

use App\Entity\BudgetRequest;
use App\Entity\Category;
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

    /** @var Category */
    private $category;

    /** @var ManagerRegistry */
    private $managerRegistry;

    /** @var BudgetRequestRepository */
    private $budgetRequestRepository;

    /** @var BudgetRequest */
    private $budgetRequest;

    /** @var string */
    private $title;

    /** @var string */
    private $description;

    /** @var int */
    private $categoryId;

    /** @var string */
    private $status;


    public function __construct(
        UserService $userService,
        CategoryRepository $categoryRepository,
        BudgetRequestRepository $budgetRequestRepository,
        ManagerRegistry $managerRegistry)
    {
        $this->userService = $userService;
        $this->categoryRepository = $categoryRepository;
        $this->budgetRequestRepository = $budgetRequestRepository;
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

        $this->budgetRequest = new BudgetRequest($title, $description, $this->category, $user);

        $this->saveBudgetRequest();

        return $this->budgetRequest;
    }

    /**
     * @param int $budgetRequestId
     * @param array $modificationsArray
     * @return BudgetRequest
     * @throws Exception
     */
    public function modifyBudgetRequest(int $budgetRequestId, array $modificationsArray)
    {
        if(null == $modificationsArray)
        {
            throw new Exception('Empty changes array', 400);
        }

        $this->getBudgetRequestById($budgetRequestId);

        $this->prepareModificationData($modificationsArray);

        if($this->sameBudgetRequestInfo())
        {
            throw new Exception('No changes made', 400);
        }

        if(null != $this->categoryId && $this->categoryId != $this->getCategoryId())
        {
            $this->getCategoryById($this->categoryId);
        }

        $this->budgetRequest->setTitle($this->title);
        $this->budgetRequest->setDescription($this->description);
        $this->budgetRequest->setCategory($this->category);
        $this->budgetRequest->setStatus($this->status);

        $this->saveBudgetRequest();

        return $this->budgetRequest;
    }

    /**
     * @param int $categoryId
     * @throws Exception
     */
    private function getCategoryById(int $categoryId)
    {
        $this->category = $this->categoryRepository->findCategoryById($categoryId);

        if (null == $this->category)
        {
            throw new Exception('Category ' . $categoryId . ' does not exists', 400);
        }
    }

    /**
     * @param int $budgetRequestId
     * @throws Exception
     */
    private function getBudgetRequestById(int $budgetRequestId)
    {
        $this->budgetRequest = $this->budgetRequestRepository->findBudgetRequestById($budgetRequestId);

        if (null == $this->budgetRequest)
        {
            throw new Exception('Budget request ' . $budgetRequestId . ' not exist', 400);
        }
    }

    private function saveBudgetRequest()
    {
        $entityManager = $this->managerRegistry->getManagerForClass(BudgetRequest::class);
        $entityManager->persist($this->budgetRequest);
        $entityManager->flush();
    }

    /**
     * @param array $modificationsArray
     * @throws Exception
     */
    private function prepareModificationData(array $modificationsArray)
    {
        $this->title = $this->getFieldValue(
            $modificationsArray,
            'title',
            $this->budgetRequest->getTitle());

        $this->description = $this->getFieldValue(
            $modificationsArray,
            'description',
            $this->budgetRequest->getDescription(),
            true);

        $this->categoryId = $this->getFieldValue(
            $modificationsArray,
            'category_id',
            $this->getCategoryId());

        $this->status = $this->getFieldValue(
            $modificationsArray,
            'status',
            $this->budgetRequest->getStatus());
    }

    /**
     * @param array $arrayData
     * @param string $fieldName
     * @param string $defaultValue
     * @param bool $required
     * @return string
     * @throws Exception
     */
    private function getFieldValue(
        array $arrayData,
        string $fieldName,
        ?string $defaultValue,
        bool $required = false):?string
    {
        $fieldValue = isset($arrayData[$fieldName]) ? $arrayData[$fieldName] : $defaultValue;

        if(null == $fieldValue && $required)
        {
            throw new Exception('Required field missing' , 400);
        }

        return $fieldValue;
    }

    private function sameBudgetRequestInfo():bool
    {
         return
            $this->title == $this->budgetRequest->getTitle() &&
            $this->description == $this->budgetRequest->getDescription() &&
            $this->categoryId == $this->getCategoryId() &&
            $this->status == $this->budgetRequest->getStatus();
    }

    private function getCategoryId():?int
    {
        $category = $this->budgetRequest->getCategory();

        $categoryId = null;

        if(null != $category)
        {
            $categoryId = $category->getId();
        }

        return $categoryId;
    }
}