<?php


namespace App\DataFixtures;


use App\Api\Action\BudgetRequest\Status;
use App\Entity\BudgetRequest;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DataFixtures implements FixtureInterface
{
    //Constants will be used as fake data here and for functional and unit tests
    public const USER_ID = 1;
    public const USER_EMAIL = 'user@mail.com';
    public const USER_INVALID_EMAIL = 'user@';
    public const USER_PHONE = 555475514;
    public const USER_OLD_PHONE = 555098765;
    public const USER_PHONE_AS_NEGATIVE_INTEGER = -555475578;
    public const USER_PHONE_AS_FLOAT = 47.55;
    public const USER_NON_NUMERIC_PHONE = '+34-555 47 55 14';
    public const USER_ADDRESS = 'Calle Mayor 1 1A';

    public const CATEGORY_ID = 1;
    public const CATEGORY_INVALID_ID = 99999;
    public const CATEGORY_NON_NUMERIC_ID = 'A1';
    public const CATEGORY_NEGATIVE_ID = -2;
    public const CATEGORY_FLOAT_ID = 2.22;
    public const CATEGORY_NAME = 'Categoría 1';
    public const CATEGORY_ARRAY = ['Categoría 1', 'Categoría 2', 'Categoría 3', 'Categoría 4'];
    public const CATEGORY_NAME_FOR_BUDGET_REQUEST = 'Categoría 5';

    public const BUDGET_REQUEST_ID = 1;
    public const DISCARDED_BUDGET_REQUEST_ID = 2;
    public const BUDGET_REQUEST_WITHOUT_TITLE_ID = 3;
    public const BUDGET_REQUEST_WITHOUT_CATEGORY_ID = 4;
    public const BUDGET_REQUEST_INVALID_ID = 99999;
    public const BUDGET_REQUEST_NON_NUMERIC_ID = 'A55';
    public const BUDGET_REQUEST_NEGATIVE_ID = -2;
    public const BUDGET_REQUEST_FLOAT_ID = 2.22;
    public const BUDGET_REQUEST_TITLE = 'Título solicitud 1';
    public const BUDGET_REQUEST_NEW_TITLE = 'Nuevo título solicitud 1';
    public const BUDGET_REQUEST_DESCRIPTION = 'Descripción solicitud 1';

    public const TOO_LONG_TEXT =
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
       Morbi dictum eros ac purus eleifend rutrum. Morbi malesuada tincidunt nibh. 
       Curabitur in fermentum dui, quis sagittis purus. Nulla viverra sodales lacus, vel tincidunt elit malesuada eu. 
       Mauris molestie dolor non neque posuere vehicula. Interdum et malesuada fames ac ante ipsum primis in faucibus. 
       In sollicitudin, elit nec bibendum placerat, nisl tellus vulputate sem, eu hendrerit nulla nisi et turpis. 
       Curabitur placerat tincidunt nibh, et consequat purus luctus vitae. 
       Praesent egestas felis quis lectus hendrerit, vel ullamcorper lectus. ';

    public const TOO_LONG_INTEGER = 555123456789123456789123456789;

    public function load(ObjectManager $manager)
    {
        $this->resetAutoIncrement($manager, 'user');
        $this->resetAutoIncrement($manager, 'category');
        $this->resetAutoIncrement($manager, 'budget_request');

        $user = $this->createUser($manager);
        $this->createCategories($manager);

        $category = $this->createCategory($manager, self::CATEGORY_NAME_FOR_BUDGET_REQUEST, null);

        $this->createBudgetRequest($manager, self::BUDGET_REQUEST_TITLE, $category, $user);
        $this->createBudgetRequest($manager, self::BUDGET_REQUEST_TITLE, $category, $user, true);
        $this->createBudgetRequest($manager, null, $category, $user);
        $this->createBudgetRequest($manager, self::BUDGET_REQUEST_TITLE, null, $user, true);
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

    private function createCategory(
        ObjectManager $manager,
        string $categoryName,
        ?string $categoryDescription = null
    ): Category
    {
        $category = new Category($categoryName, $categoryDescription);

        $manager->persist($category);
        $manager->flush();

        return $category;
    }

    private function createCategories(ObjectManager $manager)
    {
        foreach (self::CATEGORY_ARRAY as $categoryName){

            $this->createCategory($manager, $categoryName);
        }
    }

    private function createBudgetRequest(
        ObjectManager $manager,
        ?string $title,
        ?Category $category,
        User $user,
        bool $discarded = false)
    {
        $budgetRequest = new BudgetRequest(
            $title,
            self::BUDGET_REQUEST_DESCRIPTION,
            $category,
            $user
        );

        if($discarded)
        {
            $budgetRequest->setStatus(Status::STATUS_DISCARDED);
        }

        $manager->persist($budgetRequest);
        $manager->flush();
    }
}