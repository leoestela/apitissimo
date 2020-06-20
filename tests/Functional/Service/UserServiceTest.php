<?php


namespace App\Tests\Functional\Service;

use App\DataFixtures\UserFixtures;
use App\Entity\User;
use App\Tests\Functional\FunctionalWebTestCase;
use Exception;

class UserServiceTest extends FunctionalWebTestCase
{
    private const EMAIL = 'leoestela@hotmail.com';
    private const PHONE = '971100309';
    private const ADDRESS = 'Batle Biel Bibiloni 2 2B';

    /** @var User */
    private $user;

    /** @throws Exception */
    public function setUp()
    {
        parent::setUp();

        $this->user = static::$container->get('user_service')->actualizeUser(self::EMAIL, self::PHONE, self::ADDRESS);
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
        $this->assertEquals(self::EMAIL, $this->user->getEmail());
        $this->assertEquals(self::PHONE, $this->user->getPhone());
        $this->assertEquals(self::ADDRESS, $this->user->getAddress());
    }

    /** @throws Exception */
    public function tearDown()
    {
        $this->purge();
    }
}