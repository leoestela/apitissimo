<?php


namespace App\Tests\Unit\Service;


use App\Entity\BudgetRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\BudgetRequestService;
use App\Service\UserService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Exception;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class BudgetRequestServiceTest extends TestCase
{
    private const DESCRIPTION = 'Descripción de la solicitud de presupuesto.';
    private const EMAIL = 'leoestela@hotmail.com';
    private const PHONE = '+34971100309';
    private const ADDRESS = 'Batle Biel Bibiloni 2 2B';

    /** @var BudgetRequestService */
    private $budgetRequestService;

    /**  @var ObjectProphecy|User */
    private $userProphecy;

    /** @var ObjectProphecy|UserService */
    private $userServiceProphecy;

    /**  @var ObjectProphecy|ManagerRegistry */
    private $managerRegistryProphecy;

    /**  @var ObjectProphecy|ObjectManager */
    private $entityManagerProphecy;


    public function setUp()
    {
        parent::setUp();

        $this->userProphecy = $this->prophesize(User::class);

        $this->userServiceProphecy = $this->prophesize(UserService::class);
        $userService = $this->userServiceProphecy->reveal();

        $this->managerRegistryProphecy = $this->prophesize(ManagerRegistry::class);
        $managerRegistry = $this->managerRegistryProphecy->reveal();

        $this->entityManagerProphecy = $this->prophesize(ObjectManager::class);
        $this->entityManagerProphecy = $this->entityManagerProphecy->willBeConstructedWith([BudgetRequest::class]);

        $this->budgetRequestService = new BudgetRequestService($userService, $managerRegistry);
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfDescriptionIsNull()
    {
        $this->aExceptionIsExpected();

        $this->budgetRequestService->createBudgetRequest(
            null,
            '',
            null,
            self::EMAIL,
            self::PHONE,
            self::ADDRESS);
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfEmailIsNull()
    {
        $this->aExceptionIsExpected();

        $this->budgetRequestService->createBudgetRequest(
            null,
            self::DESCRIPTION,
            null,
            '',
            self::PHONE,
            self::ADDRESS);
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfPhoneIsNull()
    {
        $this->aExceptionIsExpected();

        $this->budgetRequestService->createBudgetRequest(
            null,
            self::DESCRIPTION,
            null,
            self::EMAIL,
            '',
            self::ADDRESS);
    }

    /** @throws Exception */
    public function testShouldThrowExceptionIfAddressIsNull()
    {
        $this->aExceptionIsExpected();

        $this->budgetRequestService->createBudgetRequest(
            null,
            self::DESCRIPTION,
            null,
            self::EMAIL,
            self::PHONE,
            '');
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfEmailIsNotValid()
    {
        $this->aExceptionIsExpected();

        $this->budgetRequestService->createBudgetRequest(
            null,
            self::DESCRIPTION,
            null,
            'antoniahernandez55@',
            self::PHONE,
            self::ADDRESS);
    }

    public function testShouldCreateBudgetRequestWithoutCategory()
    {
        try {
            $this->userServiceProphecy
                ->actualizeUser(self::EMAIL, self::PHONE, self::ADDRESS)
                ->shouldBeCalledOnce()
                ->willReturn($this->userProphecy);
        } catch (Exception $exception)
        {
            $this->fail($exception->getMessage());
        }

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

        try
        {
            $this->budgetRequestService->createBudgetRequest(
                null,
                self::DESCRIPTION,
                null,
                self::EMAIL,
                self::PHONE,
                self::ADDRESS);
        } catch (Exception $exception)
        {
            $this->fail($exception->getMessage());
        }
    }

    public function aExceptionIsExpected()
    {
        $this->expectException(Exception::class);
    }
}