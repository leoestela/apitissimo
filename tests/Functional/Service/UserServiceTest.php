<?php


namespace App\Tests\Functional\Service;

use App\DataFixtures\UserFixtures;
use App\Entity\User;
use App\Tests\Functional\FunctionalWebTestCase;
use Exception;

class UserServiceTest extends FunctionalWebTestCase
{
    private const USER_EMAIL = 'leoestela@hotmail.com';
    private const USER_PHONE = '971100309';
    private const USER_ADDRESS = 'Batle Biel Bibiloni 2 2B';

    /** @var User */
    private $user;

    /** @throws Exception */
    public function setUp()
    {
        parent::setUp();

        $this->user = static::$container->get('user_service')->actualizeUser(
            self::USER_EMAIL,
            self::USER_PHONE,
            self::USER_ADDRESS);
    }

    public function testActualizeNonExistingUserCreatesUser()
    {
        $this->usersAreEquals();
    }

    public function testActualizeExistingUserModifiesUser()
    {
        $this->loadFixtures(new UserFixtures());

        $this->usersAreEquals();
    }

    public function usersAreEquals()
    {
        $this->assertEquals(self::USER_EMAIL, $this->user->getEmail());
        $this->assertEquals(self::USER_PHONE, $this->user->getPhone());
        $this->assertEquals(self::USER_ADDRESS, $this->user->getAddress());
    }

    /** @throws Exception */
    public function tearDown()
    {
        $this->purge();
    }
}