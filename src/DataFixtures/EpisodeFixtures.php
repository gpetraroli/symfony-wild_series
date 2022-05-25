<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use App\Entity\Season;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public const N_EPISODES = 24;

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for($i = 0; $i < ProgramFixtures::N_PROGRAMS; $i++) {
            for ($j = 0; $j < SeasonFixtures::N_SEASONS; $j++) {
                for ($k = 0; $k < $this::N_EPISODES; $k++) {
                    $episode = new Episode();
                    $episode->setTitle($faker->company);
                    $episode->setSlug($this->slugify->generate($episode->getTitle()));
                    $episode->setNumber($k+1);
                    $episode->setSynopsis($faker->realText());
                    $episode->setSeason($this->getReference('season-' . $i . '-' . $j));
                    $manager->persist($episode);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            SeasonFixtures::class
        ];
    }
}
