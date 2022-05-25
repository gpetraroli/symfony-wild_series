<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public const N_SEASONS = 12;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for($i = 0; $i < ProgramFixtures::N_PROGRAMS; $i++) {
            for ($j = 0; $j < $this::N_SEASONS; $j++) {
                $season = new Season();
                $season->setProgram($this->getReference('program-' . $i));
                $season->setDescription($faker->realText);
                $season->setNumber($j + 1);
                $season->setYear($faker->year());

                $manager->persist($season);

                $this->addReference('season-' . $i . '-' . $j, $season);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProgramFixtures::class,
        ];
    }
}
