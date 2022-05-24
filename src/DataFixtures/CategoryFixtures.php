<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    const CATEGORIES = [
        'Action',
        'Adventure',
        'Motion picture',
        'Sci-Fi',
        'Horror',
    ];

    public function load(ObjectManager $manager)
    {
        foreach ($this::CATEGORIES as $el) {
            $category = new Category();
            $category->setName($el);
            $manager->persist($category);
            $this->addReference('category_' . $el, $category);
        }

        $manager->flush();
    }
}
