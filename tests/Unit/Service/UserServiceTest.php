<?php

namespace App\Tests\Unit\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Exception;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class UserServiceTest extends ServiceTestCase
{
    protected const USER_PHONE_OLD = '+34971473858';

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

        $this->userService->actualizeUser('', self::USER_PHONE, self::USER_ADDRESS);
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfPhoneIsNull()
    {
        $this->aExceptionIsExpected();

        $this->userService->actualizeUser(self::USER_EMAIL,'',self::USER_ADDRESS);
    }

    /** @throws Exception */
    public function testShouldThrowExceptionIfAddressIsNull()
    {
        $this->aExceptionIsExpected();

        $this->userService->actualizeUser(self::USER_EMAIL,self::USER_PHONE,'');
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfEmailIsNotValid()
    {
        $this->aExceptionIsExpected();

        $this->userService->actualizeUser('leoestela@', self::USER_PHONE,self::USER_ADDRESS);
    }

    /** @throws  Exception */
    public function testShouldReturnUserIfExistsAndNoDataChanged()
    {
        $this->mockFindUserWillReturnParameterValue($this->userProphecy);

        $this->userProphecy->getPhone()->shouldBeCalledOnce()->willReturn(self::USER_PHONE);
        $this->userProphecy->getAddress()->shouldBeCalledOnce()->willReturn(self::USER_ADDRESS);

        $this->userProphecy->setPhone(self::USER_PHONE)->shouldNotBeCalled();
        $this->userProphecy->setAddress(self::USER_ADDRESS)->shouldNotBeCalled();

        $this->managerRegistryProphecy->getManagerForClass('App\Entity\User')->shouldNotBeCalled();

        $this->entityManagerProphecy->persist($this->userProphecy)->shouldNotBeCalled();
        $this->entityManagerProphecy->flush()->shouldNotBeCalled();

        $this->tryActualizeUserWithValidData();
    }

    /** @throws  Exception */
    public function testShouldModifyAndReturnUserIfExistsAndDataChanged()
    {
        $this->mockFindUserWillReturnParameterValue($this->userProphecy);

        $this->userProphecy->getPhone()->shouldBeCalledOnce()->willReturn(self::USER_PHONE_OLD);

        $this->userProphecy->setPhone(self::USER_PHONE)->shouldBeCalledOnce();
        $this->userProphecy->setAddress(self::USER_ADDRESS)->shouldBeCalledOnce();

        $this->mockGetManagerForUserForBeCalledOnce();

        $this->entityManagerProphecy->persist($this->userProphecy)->shouldBeCalledOnce();

        $this->mockFlushForBeCalledOnce();

        $this->tryActualizeUserWithValidData();
    }

    /** @throws  Exception */
    public function testShouldCreateAndReturnUserIfNotExists()
    {
        $this->mockFindUserWillReturnParameterValue(null);

        $this->userProphecy->getPhone()->shouldNotBeCalled();
        $this->userProphecy->getAddress()->shouldNotBeCalled();

        $this->userProphecy->setPhone(self::USER_PHONE)->shouldNotBeCalled();
        $this->userProphecy->setAddress(self::USER_ADDRESS)->shouldNotBeCalled();

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

    public function mockFindUserWillReturnParameterValue(?ObjectProphecy $returns)
    {
        $this->userRepositoryProphecy
            ->findOneByEmail(self::USER_EMAIL)
            ->shouldBeCalledOnce()
            ->willReturn($returns);
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
                ->actualizeUser(self::USER_EMAIL,self::USER_PHONE,self::USER_ADDRESS);

            $this->assertInstanceOf('App\Entity\User', $modifiedUser);

        } catch (Exception $exception) {
            $this->fail($exception->getMessage());
        }
    }
}

