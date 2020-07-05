<?php


namespace App\Tests\Functional;

use App\DataFixtures\DataFixtures;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class FunctionalWebTestCase extends WebTestCase
{
    /** @var ContainerInterface */
    protected static $container;

    /** @var ORMExecutor */
    protected $executor;

    public function setUp()
    {
        parent::setUp();

        $kernel = static::createKernel();
        $kernel->boot();

        static::$container = $kernel->getContainer();

        /** @var  EntityManagerInterface */
        $entityManager = static::$container->get('doctrine')->getManager();

        $purger = new ORMPurger();

        $this->executor = new ORMExecutor($entityManager, $purger);
    }

    public function loadFixtures()
    {
        $loader = new Loader();
        $loader->addFixture(new DataFixtures());

        $this->executor->execute($loader->getFixtures());
    }

    public function sendRequest(string $method, string $uri): Response
    {
        $client = static::createClient();
        $client->request($method, $uri);

        return $client->getResponse();
    }

    /** @throws Exception */
    public function purge()
    {
        $this->executor->purge();
    }
}