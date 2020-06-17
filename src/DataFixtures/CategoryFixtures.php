<?php


namespace App\DataFixtures;


use App\Entity\Category;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $categoryCollection =
            ['Calefacción', 'Reformas Cocinas', 'Reformas Baños', 'Aire Acondicionado', 'Contrucción Casas'];

        foreach ($categoryCollection as $categoryName){

            $category = new Category($categoryName, null);

            $manager->persist($category);
            $manager->flush();
        }
    }
}