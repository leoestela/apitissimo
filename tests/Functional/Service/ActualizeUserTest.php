<?php


namespace App\Tests\Functional\Service;

use App\DataFixtures\UserFixtures;
use App\Entity\User;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ActualizeUserTest extends WebTestCase
{
    private const EMAIL = 'leoestela@hotmail.com';
    private const PHONE = '971100309';
    private const ADDRESS = 'Batle Biel Bibiloni 2 2B';

    /** @var User */
    private $user;

    /** @var ORMExecutor */
    private $executor;

    /** @throws Exception */
    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $container = $kernel->getContainer();

        $entityManager = $container->get('doctrine')->getManager();

        $purger = new ORMPurger();

        $this->executor = new ORMExecutor($entityManager, $purger);

        $this->user = $container->get('user_service')->actualizeUser(self::EMAIL, self::PHONE, self::ADDRESS);

        parent::setUp();
    }

    public function testActualizeNonExistingUserCreatesUser()
    {
        $this->usersAreEquals();
    }

    public function testActualizeExistingUserModifiesUser()
    {
        $loader = new Loader();
        $loader->addFixture(new UserFixtures());

        $this->executor->execute($loader->getFixtures());

        $this->usersAreEquals();
    }

    public function usersAreEquals()
    {
        $this->assertEquals(self::EMAIL, $this->user->getEmail());
        $this->assertEquals(self::PHONE, $this->user->getPhone());
        $this->assertEquals(self::ADDRESS, $this->user->getAddress());
    }

    /** @throws Exception */
    public function tearDown()
    {
        $this->executor->purge();
    }
}