<?php

namespace App\Tests\Unit\Service;

use App\Service\UserService;
use Exception;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    private const EMAIL = 'leoestela@hotmail.com';
    private const PHONE = '+34971100309';
    private const ADDRESS = 'Batle Biel Bibiloni 2 2B';

    /** @var UserService */
    private $userService;

    public function setUp()
    {
        $this->userService = new UserService();
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
}

