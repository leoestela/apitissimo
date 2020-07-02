<?php


namespace App\Tests\Unit\Service;


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
    private const USER_INVALID_EMAIL = 'usuario@';
    private const CATEGORY_ID = 1;
    private const CATEGORY_INVALID_ID = 999999;
    private const CATEGORY_NAME = 'Categoría 1';
    private const BUDGET_REQUEST_ID = 1;
    private const BUDGET_REQUEST_TITLE = 'Título de la solicitud';
    private const BUDGET_REQUEST_DESCRIPTION = 'Descripción de la solicitud de presupuesto.';

    /** @var BudgetRequestService */
    private $budgetRequestService;

    /** @var ObjectProphecy|UserService */
    private $userServiceProphecy;

    /** @var ObjectProphecy|CategoryRepository */
    private $categoryRepositoryProphecy;

    /** @var ObjectProphecy|CategoryRepository */
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
            $userService, $categoryRepository, $budgetRequestRepository, $this->managerRegistry);
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfDescriptionIsNullWhenCreatesBudgetRequest()
    {
        $this->aExceptionIsExpected();

        $this->budgetRequestService->createBudgetRequest(
            null,
            '',
            null,
            self::USER_EMAIL,
            self::USER_PHONE,
            self::USER_ADDRESS);
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfEmailIsNullWhenCreatesBudgetRequest()
    {
        $this->aExceptionIsExpected();

        $this->budgetRequestService->createBudgetRequest(
            null,
            self::BUDGET_REQUEST_DESCRIPTION,
            null,
            '',
            self::USER_PHONE,
            self::USER_ADDRESS);
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfPhoneIsNullWhenCreatesBudgetRequest()
    {
        $this->aExceptionIsExpected();

        $this->budgetRequestService->createBudgetRequest(
            null,
            self::BUDGET_REQUEST_DESCRIPTION,
            null,
            self::USER_EMAIL,
            '',
            self::USER_ADDRESS);
    }

    /** @throws Exception */
    public function testShouldThrowExceptionIfAddressIsNullWhenCreatesBudgetRequest()
    {
        $this->aExceptionIsExpected();

        $this->budgetRequestService->createBudgetRequest(
            null,
            self::BUDGET_REQUEST_DESCRIPTION,
            null,
            self::USER_EMAIL,
            self::USER_PHONE,
            '');
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfEmailIsNotValidWhenCreatesBudgetRequest()
    {
        $this->aExceptionIsExpected();

        $this->budgetRequestService->createBudgetRequest(
            null,
            self::BUDGET_REQUEST_DESCRIPTION,
            null,
            self::USER_INVALID_EMAIL,
            self::USER_PHONE,
            self::USER_ADDRESS);
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfCategoryPassedNotExistsWhenCreatesBudgetRequest()
    {
        $this->mockActualizeUser();

        $this->aExceptionIsExpected();

        $this->budgetRequestService->createBudgetRequest(
            null,
            self::BUDGET_REQUEST_DESCRIPTION,
            self::CATEGORY_INVALID_ID,
            self::USER_EMAIL,
            self::USER_PHONE,
            self::USER_ADDRESS);
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
                self::BUDGET_REQUEST_DESCRIPTION,
                null,
                self::USER_EMAIL,
                self::USER_PHONE,
                self::USER_ADDRESS);
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
                self::BUDGET_REQUEST_DESCRIPTION,
                self::CATEGORY_ID,
                self::USER_EMAIL,
                self::USER_PHONE,
                self::USER_ADDRESS);
        } catch (Exception $exception)
        {
            $this->fail($exception->getMessage());
        }
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfModificationsArrayIsNullWhenModify()
    {
        $this->aExceptionIsExpected();

        $this->budgetRequestService->modifyBudgetRequest(self::BUDGET_REQUEST_ID,[]);
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfBudgetRequestToModifyNotExists()
    {
        $this->budgetRequestRepositoryProphecy
            ->findBudgetRequestById(self::BUDGET_REQUEST_ID)->shouldBeCalledOnce()->willReturn(null);

        $this->aExceptionIsExpected();

        $this->budgetRequestService->modifyBudgetRequest(self::BUDGET_REQUEST_ID,['title' => null]);
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfNoChangesPassedWhenModify()
    {
        $this->mockFindBudgetRequest();

        $this->aExceptionIsExpected();

        $this->budgetRequestService->modifyBudgetRequest(
            self::BUDGET_REQUEST_ID,
            ['title' => self::BUDGET_REQUEST_TITLE]);
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfNewCategoryNotExistsWhenModify()
    {
        $this->mockFindBudgetRequest();

        $this->categoryRepositoryProphecy
            ->findCategoryById(self::CATEGORY_INVALID_ID)->shouldBeCalledOnce()->willReturn(null);

        $this->aExceptionIsExpected();

        $this->budgetRequestService->modifyBudgetRequest(
            self::BUDGET_REQUEST_ID,
            ['title' => self::BUDGET_REQUEST_TITLE, 'category_id' => self::CATEGORY_INVALID_ID]);
    }

    /** @throws  Exception */
    public function testShouldModifyBudgetRequestIfDataValid()
    {
        $this->mockFindBudgetRequest();

        $this->mockFindCategory();

        $this->mockSaveBudgetRequest();

        try
        {
            $this->budgetRequestService->modifyBudgetRequest(
                self::BUDGET_REQUEST_ID,
                ['title' => self::BUDGET_REQUEST_TITLE, 'category_id' => self::CATEGORY_ID]);
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
                ->actualizeUser(self::USER_EMAIL, self::USER_PHONE, self::USER_ADDRESS)
                ->shouldBeCalledOnce()
                ->willReturn($this->userProphecy);
        } catch (Exception $exception)
        {
            $this->fail($exception->getMessage());
        }
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
        $category = new Category(self::CATEGORY_NAME, null);

        $this->categoryRepositoryProphecy
            ->findCategoryById(self::CATEGORY_ID)->shouldBeCalledOnce()->willReturn($category);
    }

    private function mockFindBudgetRequest()
    {
        $user = new User(self::USER_EMAIL, self::USER_PHONE, self::USER_ADDRESS);

        $budgetRequest = new BudgetRequest(
            self::BUDGET_REQUEST_TITLE,
            self::BUDGET_REQUEST_DESCRIPTION,
            null,
            $user);

        $this->budgetRequestRepositoryProphecy
            ->findBudgetRequestById(self::BUDGET_REQUEST_ID)->shouldBeCalledOnce()->willReturn($budgetRequest);
    }
}