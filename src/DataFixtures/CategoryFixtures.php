<?php


namespace App\DataFixtures;


use App\Entity\Category;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $this->resetAutoIncrement($manager);

        $categoryCollection =
            ['Calefacción', 'Reformas Cocinas', 'Reformas Baños', 'Aire Acondicionado', 'Contrucción Casas'];

        foreach ($categoryCollection as $categoryName){

            $category = new Category($categoryName, null);

            $manager->persist($category);
            $manager->flush();
        }
    }

    public function resetAutoIncrement(ObjectManager $manager)
    {
        $connection = $manager->getConnection();
        $connection->exec("ALTER TABLE category AUTO_INCREMENT = 1;");
    }
}