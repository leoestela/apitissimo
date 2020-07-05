<?php


namespace App\DataFixtures;


use App\Entity\BudgetRequest;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DataFixtures implements FixtureInterface
{
    public const USER_EMAIL = 'user@mail.com';
    public const USER_INVALID_EMAIL = 'user@';
    public const USER_PHONE = '5551234567';
    public const USER_OLD_PHONE = '555098765';
    public const USER_ADDRESS = 'Calle Mayor 1 1A';

    public const CATEGORY_ID = 1;
    public const CATEGORY_INVALID_ID = 99999;
    public const CATEGORY_NAME = 'Categoría 1';
    public const CATEGORY_ARRAY = ['Categoría 1', 'Categoría 2', 'Categoría 3', 'Categoría 4'];

    public const BUDGET_REQUEST_ID = 1;
    public const BUDGET_REQUEST_INVALID_ID = 99999;
    public const BUDGET_REQUEST_TITLE = 'Título solicitud 1';
    public const BUDGET_REQUEST_NEW_TITLE = 'Nuevo título solicitud 1';
    public const BUDGET_REQUEST_OLD_TITLE = 'Titulo antiguo solicitud 1';
    public const BUDGET_REQUEST_DESCRIPTION = 'Descripción solicitud 1';
    public const BUDGET_REQUEST_TOO_LONG_DESCRIPTION = 'Lorem ipsum';

    public function load(ObjectManager $manager)
    {
        $this->resetAutoIncrement($manager, 'user');
        $this->resetAutoIncrement($manager, 'category');
        $this->resetAutoIncrement($manager, 'budget_request');

        $user = $this->createUser($manager);
        $this->createCategories($manager);
        $this->createBudgetRequest($manager, $user);
    }

    private function resetAutoIncrement(ObjectManager $manager, string $tableName)
    {
        $connection = $manager->getConnection();
        $connection->exec('ALTER TABLE ' . $tableName . ' AUTO_INCREMENT = 1;');
    }

    private function createUser(ObjectManager $manager): User
    {
        $user = new User(self::USER_EMAIL, self::USER_PHONE, self::USER_ADDRESS);

        $manager->persist($user);
        $manager->flush();

        return $user;
    }

    private function createCategories(ObjectManager $manager)
    {
        foreach (self::CATEGORY_ARRAY as $categoryName){

            $category = new Category($categoryName, null);

            $manager->persist($category);
            $manager->flush();
        }
    }

    private function createBudgetRequest(ObjectManager $manager, User $user)
    {
        $budgetRequest = new BudgetRequest(
            self::BUDGET_REQUEST_TITLE,
            self::BUDGET_REQUEST_DESCRIPTION,
            null,
            $user
        );

        $manager->persist($budgetRequest);
        $manager->flush();
    }
}