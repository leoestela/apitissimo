<?php


namespace App\Tests\Unit\Service;


use App\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Exception;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

abstract class ServiceTestCase extends TestCase
{
    /**  @var ObjectProphecy|User */
    protected $userProphecy;

    /**  @var ObjectProphecy|ManagerRegistry */
    protected $managerRegistryProphecy;

    /** @var ManagerRegistry */
    protected $managerRegistry;

    /**  @var ObjectProphecy|ObjectManager */
    protected $entityManagerProphecy;


    public function setUp()
    {
        parent::setUp();

        $this->userProphecy = $this->prophesize(User::class);

        $this->managerRegistryProphecy = $this->prophesize(ManagerRegistry::class);
        $this->managerRegistry = $this->managerRegistryProphecy->reveal();

        $this->entityManagerProphecy = $this->prophesize(ObjectManager::class);
        $this->entityManagerProphecy = $this->entityManagerProphecy->willBeConstructedWith([$this->getClass()]);
    }

    abstract protected static function getClass(): string;

    public function aExceptionIsExpected()
    {
        $this->expectException(Exception::class);
    }
}