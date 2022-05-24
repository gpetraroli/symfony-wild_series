<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $program = new Program();

        $charSet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        for ($i = 0; $i < 5; $i++) {
            $program->setTitle($this->randStr($charSet, 10));
            $program->setSynopsis($this->randStr($charSet, 100));
            $program->setPoster("https://resizing.flixster.com/3vRP1nkXLAN-luYoisSPrJRaLNE=/206x305/v2/https://flxt.tmsimg.com/assets/p20492187_b_v8_ah.jpg");
            $program->setCategory($this->getReference('category_Action'));
            $manager->persist($program);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
        ];
    }

    private function randStr(string $charSet, int $length): string
    {
        $randstring = '';
        for ($i = 0; $i < $length; $i++) {
            $randstring = $charSet[rand(0, strlen($charSet))];
        }
        return $randstring;
    }
}
