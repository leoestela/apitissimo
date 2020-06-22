<?php


namespace App\Tests\Unit\Service;


use App\Entity\BudgetRequest;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Service\BudgetRequestService;
use App\Service\UserService;
use Exception;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class BudgetRequestServiceTest extends ServiceTestCase
{
    private const CATEGORY_DESCRIPTION = 'Descripción de la solicitud de presupuesto.';
    private const CATEGORY_ID = 1;

    /** @var BudgetRequestService */
    private $budgetRequestService;

    /** @var ObjectProphecy|UserService */
    private $userServiceProphecy;

    /** @var ObjectProphecy|CategoryRepository */
    private $categoryRepositoryProphecy;


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

        $this->budgetRequestService = new BudgetRequestService(
            $userService, $categoryRepository, $this->managerRegistry);
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfDescriptionIsNull()
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
    public function testShouldThrowExceptionIfEmailIsNull()
    {
        $this->aExceptionIsExpected();

        $this->budgetRequestService->createBudgetRequest(
            null,
            self::CATEGORY_DESCRIPTION,
            null,
            '',
            self::USER_PHONE,
            self::USER_ADDRESS);
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfPhoneIsNull()
    {
        $this->aExceptionIsExpected();

        $this->budgetRequestService->createBudgetRequest(
            null,
            self::CATEGORY_DESCRIPTION,
            null,
            self::USER_EMAIL,
            '',
            self::USER_ADDRESS);
    }

    /** @throws Exception */
    public function testShouldThrowExceptionIfAddressIsNull()
    {
        $this->aExceptionIsExpected();

        $this->budgetRequestService->createBudgetRequest(
            null,
            self::CATEGORY_DESCRIPTION,
            null,
            self::USER_EMAIL,
            self::USER_PHONE,
            '');
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfEmailIsNotValid()
    {
        $this->aExceptionIsExpected();

        $this->budgetRequestService->createBudgetRequest(
            null,
            self::CATEGORY_DESCRIPTION,
            null,
            'antoniahernandez55@',
            self::USER_PHONE,
            self::USER_ADDRESS);
    }

    public function testShouldCreateBudgetRequestWithoutCategory()
    {
        $this->mockActualizeUser();

        $this->categoryRepositoryProphecy->findCategoryById()->shouldNotBeCalled();

        $this->mockSaveNewBudgetRequest();

        try
        {
            $this->budgetRequestService->createBudgetRequest(
                null,
                self::CATEGORY_DESCRIPTION,
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

        $category = new Category('Categoria 1', null);

        $this->categoryRepositoryProphecy
            ->findCategoryById(self::CATEGORY_ID)->shouldBeCalledOnce()->willReturn($category);

        $this->mockSaveNewBudgetRequest();

        try
        {
            $this->budgetRequestService->createBudgetRequest(
                null,
                self::CATEGORY_DESCRIPTION,
                self::CATEGORY_ID,
                self::USER_EMAIL,
                self::USER_PHONE,
                self::USER_ADDRESS);
        } catch (Exception $exception)
        {
            $this->fail($exception->getMessage());
        }
    }

    public function mockActualizeUser()
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

    public function mockSaveNewBudgetRequest()
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
}