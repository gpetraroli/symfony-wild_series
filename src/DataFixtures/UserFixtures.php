<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasher $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $userAdmin = new User();
        $hashedPassword = $this->passwordHasher->hashPassword($userAdmin, '12345');
        $userAdmin->setPassword($hashedPassword);
        $userAdmin->setEmail('admin@wildseries.com');
        $userAdmin->setRoles(['ROLE_ADMIN']);

        $userEditor = new User();
        $hashedPassword = $this->passwordHasher->hashPassword($userEditor, '12345');
        $userEditor->setPassword($hashedPassword);
        $userEditor->setEmail('editor@wildseries.com');
        $userEditor->setRoles(['ROLE_EDITOR']);

        $manager->persist($userAdmin);
        $manager->persist($userEditor);

        $manager->flush();
    }
}
