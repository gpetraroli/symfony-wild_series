<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
        public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < ProgramFixtures::N_PROGRAMS; $i++) {
            $category = new Category();
            $category->setName('category-' . $i);
            $manager->persist($category);

            $this->addReference('category-' . $i, $category);
        }

        $manager->flush();
    }
}
