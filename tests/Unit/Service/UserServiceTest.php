<?php

namespace App\Tests\Unit\Service;

use App\DataFixtures\DataFixtures;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Exception;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class UserServiceTest extends ServiceTestCase
{
    /** @var UserService */
    private $userService;

    /** @var ObjectProphecy|UserRepository */
    private $userRepositoryProphecy;


    protected static function getClass(): string
    {
        return User::class;
    }

    public function setUp()
    {
        parent::setUp();

        $this->userRepositoryProphecy = $this->prophesize(UserRepository::class);
        $userRepository = $this->userRepositoryProphecy->reveal();

        $this->userService = new UserService($this->managerRegistry, $userRepository);
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfEmailIsNull()
    {
        $this->aExceptionIsExpected();

        $this->userService->actualizeUser('', DataFixtures::USER_PHONE, DataFixtures::USER_ADDRESS);
    }

    /** @throws Exception */
    public function testShouldThrowExceptionIfAddressIsNull()
    {
        $this->aExceptionIsExpected();

        $this->userService->actualizeUser(DataFixtures::USER_EMAIL,DataFixtures::USER_PHONE,'');
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfEmailIsNotValid()
    {
        $this->aExceptionIsExpected();

        $this->userService->actualizeUser(
            DataFixtures::USER_INVALID_EMAIL,
            DataFixtures::USER_PHONE,
            DataFixtures::USER_ADDRESS
        );
    }

    /** @throws  Exception */
    public function testShouldReturnUserIfExistsAndNoDataChanged()
    {
        $this->mockFindUserReturningParameterValue($this->userProphecy);

        $this->userProphecy->getPhone()->shouldBeCalledOnce()->willReturn(DataFixtures::USER_PHONE);
        $this->userProphecy->getAddress()->shouldBeCalledOnce()->willReturn(DataFixtures::USER_ADDRESS);

        $this->userProphecy->setPhone(DataFixtures::USER_PHONE)->shouldNotBeCalled();
        $this->userProphecy->setAddress(DataFixtures::USER_ADDRESS)->shouldNotBeCalled();

        $this->managerRegistryProphecy->getManagerForClass('App\Entity\User')->shouldNotBeCalled();

        $this->entityManagerProphecy->persist($this->userProphecy)->shouldNotBeCalled();
        $this->entityManagerProphecy->flush()->shouldNotBeCalled();

        $this->tryActualizeUserWithValidData();
    }

    /** @throws  Exception */
    public function testShouldModifyAndReturnUserIfExistsAndDataChanged()
    {
        $user = new User(DataFixtures::USER_EMAIL, DataFixtures::USER_OLD_PHONE, DataFixtures::USER_ADDRESS);

        $this->mockFindUserReturningParameterValue($user);

        $this->mockGetManagerForUserForBeCalledOnce();

        $this->entityManagerProphecy->persist($user)->shouldBeCalledOnce();

        $this->mockFlushForBeCalledOnce();

        $this->tryActualizeUserWithValidData();
    }

    /** @throws  Exception */
    public function testShouldCreateAndReturnUserIfNotExists()
    {
        $this->mockFindUserReturningParameterValue(null);

        $this->userProphecy->getPhone()->shouldNotBeCalled();
        $this->userProphecy->getAddress()->shouldNotBeCalled();

        $this->userProphecy->setPhone(DataFixtures::USER_PHONE)->shouldNotBeCalled();
        $this->userProphecy->setAddress(DataFixtures::USER_ADDRESS)->shouldNotBeCalled();

        $this->mockGetManagerForUserForBeCalledOnce();

        $this->entityManagerProphecy->persist(
            Argument::that(
                function(User $user):bool
                {
                    return true;
                }
            ))->shouldBeCalledOnce();

        $this->mockFlushForBeCalledOnce();

        $this->tryActualizeUserWithValidData();
    }

    public function mockFindUserReturningParameterValue($return)
    {
        $this->userRepositoryProphecy
            ->findOneByEmail(DataFixtures::USER_EMAIL)
            ->shouldBeCalledOnce()
            ->willReturn($return);
    }

    public function mockGetManagerForUserForBeCalledOnce()
    {
        $this->managerRegistryProphecy
            ->getManagerForClass('App\Entity\User')
            ->shouldBeCalledOnce()
            ->willReturn($this->entityManagerProphecy);
    }

    public function mockFlushForBeCalledOnce()
    {
        $this->entityManagerProphecy->flush()->shouldBeCalledOnce();
    }

    public function tryActualizeUserWithValidData()
    {
        try
        {
            $modifiedUser = $this->userService
                ->actualizeUser(
                    DataFixtures::USER_EMAIL,
                    DataFixtures::USER_PHONE,
                    DataFixtures::USER_ADDRESS
                );

            $this->assertInstanceOf('App\Entity\User', $modifiedUser);

        }
        catch (Exception $exception) {
            $this->fail($exception->getMessage());
        }
    }
}

