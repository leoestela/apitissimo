<?php


namespace App\Tests\Unit\Service;


use App\Api\Action\BudgetRequest\Status;
use App\DataFixtures\DataFixtures;
use App\Entity\BudgetRequest;
use App\Entity\Category;
use App\Entity\User;
use App\Repository\BudgetRequestRepository;
use App\Repository\CategoryRepository;
use App\Service\BudgetRequestService;
use App\Service\UserService;
use Exception;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class BudgetRequestServiceTest extends ServiceTestCase
{
    /** @var BudgetRequestService */
    private $budgetRequestService;

    /** @var ObjectProphecy|UserService */
    private $userServiceProphecy;

    /** @var ObjectProphecy|CategoryRepository */
    private $categoryRepositoryProphecy;

    /** @var ObjectProphecy|BudgetRequestRepository */
    private $budgetRequestRepositoryProphecy;


    protected static function getClass(): string
    {
        return BudgetRequest::class;
    }

    public function setUp()
    {
        parent::setUp();

        $this->userServiceProphecy = $this->prophesize(UserService::class);
        $userService = $this->userServiceProphecy->reveal();

        $this->categoryRepositoryProphecy = $this->prophesize(CategoryRepository::class);
        $categoryRepository = $this->categoryRepositoryProphecy->reveal();

        $this->budgetRequestRepositoryProphecy = $this->prophesize(BudgetRequestRepository::class);
        $budgetRequestRepository = $this->budgetRequestRepositoryProphecy->reveal();

        $this->budgetRequestService = new BudgetRequestService(
            $userService,
            $categoryRepository,
            $this->managerRegistry,
            $budgetRequestRepository
        );
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfDescriptionIsNullWhenCreate()
    {
        $this->aExceptionIsExpected();

        $this->budgetRequestService->createBudgetRequest(
            null,
            '',
            null,
            DataFixtures::USER_EMAIL,
            DataFixtures::USER_PHONE,
            DataFixtures::USER_ADDRESS);
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfEmailIsNullWhenCreate()
    {
        $this->aExceptionIsExpected();

        $this->budgetRequestService->createBudgetRequest(
            null,
            DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            null,
            '',
            DataFixtures::USER_PHONE,
            DataFixtures::USER_ADDRESS);
    }

    /** @throws Exception */
    public function testShouldThrowExceptionIfAddressIsNullWhenCreate()
    {
        $this->aExceptionIsExpected();

        $this->budgetRequestService->createBudgetRequest(
            null,
            DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            null,
            DataFixtures::USER_EMAIL,
            DataFixtures::USER_PHONE,
            '');
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfEmailIsNotValidWhenCreate()
    {
        $this->aExceptionIsExpected();

        $this->budgetRequestService->createBudgetRequest(
            null,
            DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            null,
            DataFixtures::USER_INVALID_EMAIL,
            DataFixtures::USER_PHONE,
            DataFixtures::USER_ADDRESS);
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfCategoryPassedNotExistsWhenCreate()
    {
        $this->mockActualizeUser();

        $this->mockCategoryNotFound();

        $this->aExceptionIsExpected();

        $this->budgetRequestService->createBudgetRequest(
            null,
            DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            DataFixtures::CATEGORY_INVALID_ID,
            DataFixtures::USER_EMAIL,
            DataFixtures::USER_PHONE,
            DataFixtures::USER_ADDRESS);
    }

    public function testShouldCreateBudgetRequestWithoutCategory()
    {
        $this->mockActualizeUser();

        $this->categoryRepositoryProphecy->findCategoryById()->shouldNotBeCalled();

        $this->mockSaveBudgetRequest();

        try
        {
            $this->budgetRequestService->createBudgetRequest(
                null,
                DataFixtures::BUDGET_REQUEST_DESCRIPTION,
                null,
                DataFixtures::USER_EMAIL,
                DataFixtures::USER_PHONE,
                DataFixtures::USER_ADDRESS);
        } catch (Exception $exception)
        {
            $this->fail($exception->getMessage());
        }
    }

    public function testShouldCreateBudgetRequestWithCategory()
    {
        $this->mockActualizeUser();

        $this->mockFindCategory();

        $this->mockSaveBudgetRequest();

        try
        {
            $this->budgetRequestService->createBudgetRequest(
                null,
                DataFixtures::BUDGET_REQUEST_DESCRIPTION,
                DataFixtures::CATEGORY_ID,
                DataFixtures::USER_EMAIL,
                DataFixtures::USER_PHONE,
                DataFixtures::USER_ADDRESS);
        }
        catch (Exception $exception)
        {
            $this->fail($exception->getMessage());
        }
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfNoChangesPassedForModify()
    {
        $this->aExceptionIsExpected();

        $this->budgetRequestService->modifyBudgetRequest(
            $this->getFakeBudgetRequest(),
            DataFixtures::BUDGET_REQUEST_TITLE,
            DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            null,
            Status::STATUS_PENDING
        );
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfNewCategoryNotExistsWhenModify()
    {
        $this->mockCategoryNotFound();

        $this->aExceptionIsExpected();

        $this->budgetRequestService->modifyBudgetRequest(
            $this->getFakeBudgetRequest(),
            DataFixtures::BUDGET_REQUEST_TITLE,
            DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            DataFixtures::CATEGORY_INVALID_ID,
            Status::STATUS_PENDING
        );
    }

    /** @throws  Exception */
    public function testShouldModifyBudgetRequestIfDataValid()
    {
        $this->mockFindCategory();

        $this->mockSaveBudgetRequest();

        try
        {
            $this->budgetRequestService->modifyBudgetRequest(
                $this->getFakeBudgetRequest(),
                DataFixtures::BUDGET_REQUEST_NEW_TITLE,
                DataFixtures::BUDGET_REQUEST_DESCRIPTION,
                DataFixtures::CATEGORY_ID,
                Status::STATUS_PENDING
            );
        }
        catch (Exception $exception)
        {
            $this->fail($exception->getMessage());
        }
    }

    private function mockActualizeUser()
    {
        try {
            $this->userServiceProphecy
                ->actualizeUser(
                    DataFixtures::USER_EMAIL,
                    DataFixtures::USER_PHONE,
                    DataFixtures::USER_ADDRESS
                )
                ->shouldBeCalledOnce()
                ->willReturn($this->userProphecy);
        }
        catch (Exception $exception)
        {
            $this->fail($exception->getMessage());
        }
    }

    private function mockCategoryNotFound()
    {
        $this->categoryRepositoryProphecy
            ->findCategoryById(DataFixtures::CATEGORY_INVALID_ID)->shouldBeCalledOnce()->willReturn(null);
    }

    private function mockSaveBudgetRequest()
    {
        $this->managerRegistryProphecy
            ->getManagerForClass('App\Entity\BudgetRequest')
            ->shouldBeCalledOnce()
            ->willReturn($this->entityManagerProphecy);

        $this->entityManagerProphecy->persist(
            Argument::that(
                function(BudgetRequest $budgetRequest):bool
                {
                    return true;
                }
            ))->shouldBeCalledOnce();
        $this->entityManagerProphecy->flush()->shouldBeCalledOnce();
    }

    private function mockFindCategory()
    {
        $category = new Category(DataFixtures::CATEGORY_NAME, null);

        $this->categoryRepositoryProphecy
            ->findCategoryById(DataFixtures::CATEGORY_ID)->shouldBeCalledOnce()->willReturn($category);
    }

    private function getFakeBudgetRequest(): BudgetRequest
    {
        $user = new User(
            DataFixtures::USER_EMAIL,
            DataFixtures::USER_PHONE,
            DataFixtures::USER_ADDRESS
        );

        return new BudgetRequest(
            DataFixtures::BUDGET_REQUEST_TITLE,
            DataFixtures::BUDGET_REQUEST_DESCRIPTION,
            null,
            $user
        );
    }
}