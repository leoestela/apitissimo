<?php

namespace App\Tests\Unit\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Exception;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class UserServiceTest extends TestCase
{
    private const EMAIL = 'leoestela@hotmail.com';
    private const PHONE = '+34971100309';
    private const PHONE_OLD = '+34971473858';
    private const ADDRESS = 'Batle Biel Bibiloni 2 2B';

    /** @var UserService */
    private $userService;

    /**  @var ObjectProphecy|User */
    private $userProphecy;

    /**  @var ObjectProphecy|ManagerRegistry */
    private $managerRegistryProphecy;

    /**  @var ObjectProphecy|ObjectManager */
    private $entityManagerProphecy;

    /** @var ObjectProphecy|UserRepository */
    private $userRepositoryProphecy;


    public function setUp()
    {
        parent::setUp();

        $this->userProphecy = $this->prophesize(User::class);

        $this->managerRegistryProphecy = $this->prophesize(ManagerRegistry::class);
        $managerRegistry = $this->managerRegistryProphecy->reveal();

        $this->entityManagerProphecy = $this->prophesize(ObjectManager::class);
        $this->entityManagerProphecy = $this->entityManagerProphecy->willBeConstructedWith([User::class]);

        $this->userRepositoryProphecy = $this->prophesize(UserRepository::class);
        $userRepository = $this->userRepositoryProphecy->reveal();

        $this->userService = new UserService($managerRegistry, $userRepository);
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfEmailIsNull()
    {
        $this->aExceptionIsExpected();

        $this->userService->actualizeUser('', self::PHONE, self::ADDRESS);
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfPhoneIsNull()
    {
        $this->aExceptionIsExpected();

        $this->userService->actualizeUser(self::EMAIL,'',self::ADDRESS);
    }

    /** @throws Exception */
    public function testShouldThrowExceptionIfAddressIsNull()
    {
        $this->aExceptionIsExpected();

        $this->userService->actualizeUser(self::EMAIL,self::PHONE,'');
    }

    /** @throws  Exception */
    public function testShouldThrowExceptionIfEmailIsNotValid()
    {
        $this->aExceptionIsExpected();

        $this->userService->actualizeUser('leoestela@', self::PHONE,self::ADDRESS);
    }

    /** @throws  Exception */
    public function testShouldReturnUserIfExistsAndNoDataChanged()
    {
        $this->userRepositoryProphecy
            ->findOneByEmail(self::EMAIL)
            ->shouldBeCalledOnce()
            ->willReturn($this->userProphecy);

        $this->userProphecy->getPhone()->shouldBeCalledOnce()->willReturn(self::PHONE);
        $this->userProphecy->getAddress()->shouldBeCalledOnce()->willReturn(self::ADDRESS);

        $this->userProphecy->setPhone(self::PHONE)->shouldNotBeCalled();
        $this->userProphecy->setAddress(self::ADDRESS)->shouldNotBeCalled();

        $this->managerRegistryProphecy->getManagerForClass('App\Entity\User')->shouldNotBeCalled();

        $this->entityManagerProphecy->persist($this->userProphecy)->shouldNotBeCalled();
        $this->entityManagerProphecy->flush()->shouldNotBeCalled();

        try
        {
            $modifiedUser = $this->userService
                ->actualizeUser(self::EMAIL,self::PHONE,self::ADDRESS);

            $this->assertInstanceOf('App\Entity\User', $modifiedUser);

        } catch (Exception $exception) {
            $this->fail($exception->getMessage());
        }
    }

    /** @throws  Exception */
    public function testShouldModifyAndReturnUserIfExistsAndDataChanged()
    {
        $this->userRepositoryProphecy
            ->findOneByEmail(self::EMAIL)
            ->shouldBeCalledOnce()
            ->willReturn($this->userProphecy);

        $this->userProphecy->getPhone()->shouldBeCalledOnce()->willReturn(self::PHONE_OLD);

        $this->userProphecy->setPhone(self::PHONE)->shouldBeCalledOnce();
        $this->userProphecy->setAddress(self::ADDRESS)->shouldBeCalledOnce();

        $this->managerRegistryProphecy
            ->getManagerForClass('App\Entity\User')
            ->shouldBeCalledOnce()
            ->willReturn($this->entityManagerProphecy);

        $this->entityManagerProphecy->persist($this->userProphecy)->shouldBeCalledOnce();
        $this->entityManagerProphecy->flush()->shouldBeCalledOnce();

        try
        {
            $modifiedUser = $this->userService
                ->actualizeUser(self::EMAIL,self::PHONE,self::ADDRESS);

            $this->assertInstanceOf('App\Entity\User', $modifiedUser);

        } catch (Exception $exception) {
            $this->fail($exception->getMessage());
        }
    }

    /** @throws  Exception */
    public function testShouldCreateAndReturnUserIfNotExists()
    {
        $this->userRepositoryProphecy->findOneByEmail(self::EMAIL)->shouldBeCalledOnce()->willReturn(null);

        $this->userProphecy->getPhone()->shouldNotBeCalled();
        $this->userProphecy->getAddress()->shouldNotBeCalled();

        $this->userProphecy->setPhone(self::PHONE)->shouldNotBeCalled();
        $this->userProphecy->setAddress(self::ADDRESS)->shouldNotBeCalled();

        $this->managerRegistryProphecy
            ->getManagerForClass('App\Entity\User')
            ->shouldBeCalledOnce()
            ->willReturn($this->entityManagerProphecy);

        $this->entityManagerProphecy->persist(
            Argument::that(
                function(User $user):bool
                {
                    return true;
                }
            ))->shouldBeCalledOnce();
        $this->entityManagerProphecy->flush()->shouldBeCalledOnce();

        try
        {
            $modifiedUser = $this->userService
                ->actualizeUser(self::EMAIL,self::PHONE,self::ADDRESS);

            $this->assertInstanceOf('App\Entity\User', $modifiedUser);

        } catch (Exception $exception) {
            $this->fail($exception->getMessage());
        }
    }

    public function aExceptionIsExpected()
    {
        $this->expectException(Exception::class);
    }
}

