<?php


namespace App\DataFixtures;


use App\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixtures implements FixtureInterface
{
    private const EMAIL = 'leoestela@hotmail.com';
    private const PHONE = '971473858';
    private const ADDRESS = 'Mare de Deu de Montserrat 47 2A';

    public function load(ObjectManager $manager)
    {
        $user = new User(self::EMAIL, self::PHONE, self::ADDRESS);

        $manager->persist($user);
        $manager->flush();
    }
}