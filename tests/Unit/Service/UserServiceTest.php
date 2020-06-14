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
    private const ADDRESS = 'Batle Biel Bibiloni 2 2B';

    /** @var UserService */
    private $userService;

    /**  @var ObjectProphecy|User */
    private $userProphecy;

    /** @var User */
    private $user;

    /**  @var ObjectProphecy|ManagerRegistry */
    private $managerRegistryProphecy;

    /** @var ManagerRegistry */
    private $managerRegistry;

    /**  @var ObjectProphecy|ObjectManager */
    private $entityManagerProphecy;

    /** @var ObjectManager */
    private $entityManager;

    /** @var ObjectProphecy|UserRepository */
    private $userRepositoryProphecy;

    /** @var UserRepository */
    private $userRepository;


    public function setUp()
    {
        $this->userProphecy = $this->prophesize(User::class);
        $this->user = $this->userProphecy->reveal();

        $this->managerRegistryProphecy = $this->prophesize(ManagerRegistry::class);
        $this->managerRegistry = $this->managerRegistryProphecy->reveal();

        $this->entityManagerProphecy = $this->prophesize(ObjectManager::class);
        $this->entityManagerProphecy = $this->entityManagerProphecy->willBeConstructedWith([User::class]);
        $this->entityManager = $this->entityManagerProphecy->reveal();

        $this->userRepositoryProphecy = $this->prophesize(UserRepository::class);
        $this->userRepository = $this->userRepositoryProphecy->reveal();

        $this->userService = new UserService($this->managerRegistry, $this->userRepository);
    }

    public function aExceptionIsExpected()
    {
        $this->expectException(Exception::class);
    }

    /** @throws */
    public function testShouldThrowExceptionIfEmailIsNull()
    {
        $this->aExceptionIsExpected();
        $this->userService->actualizeUser('', self::PHONE, self::ADDRESS);
    }

    /** @throws */
    public function testShouldThrowExceptionIfPhoneIsNull()
    {
        $this->expectException(Exception::class);
        $this->userService->actualizeUser(self::EMAIL,'',self::ADDRESS);
    }

    /** @throws */
    public function testShouldThrowExceptionIfAddressIsNull()
    {
        $this->aExceptionIsExpected();
        $this->userService->actualizeUser(self::EMAIL,self::PHONE,'');
    }

    /** @throws */
    public function testShouldThrowExceptionIfEmailIsNotValid()
    {
        $this->aExceptionIsExpected();
        $this->userService->actualizeUser('leoestela@', self::PHONE,self::ADDRESS);
    }

    /** @throws */
    public function testShouldModifyUserIfExists()
    {
        $this->userRepositoryProphecy
            ->findOneByEmail(self::EMAIL)
            ->shouldBeCalledOnce()
            ->willReturn($this->userProphecy);
        $this->userProphecy->setEmail(self::EMAIL)->shouldBeCalledOnce();
        $this->userProphecy->setPhone(self::PHONE)->shouldBeCalledOnce();
        $this->userProphecy->setAddress(self::ADDRESS)->shouldBeCalledOnce();

        $this->managerRegistryProphecy
            ->getManagerForClass('App\Entity\User')
            ->shouldBeCalledOnce()
            ->willReturn($this->entityManagerProphecy);

        $this->entityManagerProphecy->persist($this->userProphecy)->shouldBeCalledOnce();
        $this->entityManagerProphecy->flush()->shouldBeCalledOnce();

        try {
            $modifiedUser = $this->userService
                ->actualizeUser(self::EMAIL,self::PHONE,self::ADDRESS);

            $this->assertInstanceOf('App\Entity\User', $modifiedUser);

        } catch (Exception $exception) {
            $this->fail($exception->getMessage());
        }
    }

    /** @throws */
    public function testShouldCreateUserIfEmailNotExists()
    {
        $this->userRepositoryProphecy->findOneByEmail(self::EMAIL)->shouldBeCalledOnce()->willReturn(null);

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

        try {
            $modifiedUser = $this->userService
                ->actualizeUser(self::EMAIL,self::PHONE,self::ADDRESS);

            $this->assertInstanceOf('App\Entity\User', $modifiedUser);

        } catch (Exception $exception) {
            $this->fail($exception->getMessage());
        }
    }
}

