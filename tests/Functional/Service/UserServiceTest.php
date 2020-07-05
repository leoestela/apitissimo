<?php


namespace App\Tests\Functional\Service;

use App\DataFixtures\DataFixtures;
use App\Entity\User;
use App\Tests\Functional\FunctionalWebTestCase;
use Exception;

class UserServiceTest extends FunctionalWebTestCase
{
    /** @var User */
    private $user;

    /** @throws Exception */
    public function setUp()
    {
        parent::setUp();

        $this->user = static::$container->get('user_service')->actualizeUser(
            DataFixtures::USER_EMAIL,
            DataFixtures::USER_PHONE,
            DataFixtures::USER_ADDRESS);
    }

    public function testActualizeNonExistingUserCreatesUser()
    {
        $this->usersAreEquals();
    }

    public function testActualizeExistingUserModifiesUser()
    {
        $this->loadFixtures();

        $this->usersAreEquals();
    }

    public function usersAreEquals()
    {
        $this->assertEquals(DataFixtures::USER_EMAIL, $this->user->getEmail());
        $this->assertEquals(DataFixtures::USER_PHONE, $this->user->getPhone());
        $this->assertEquals(DataFixtures::USER_ADDRESS, $this->user->getAddress());
    }

    /** @throws Exception */
    public function tearDown()
    {
        $this->purge();
    }
}