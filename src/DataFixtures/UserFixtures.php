<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use DateTime;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passHasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->passHasher = $hasher;
    }


    public function load(ObjectManager $manager)
    {
        $user = new User();

        $user->setUsername('Test');
        $user->setPassword($this->passHasher->hashPassword($user, 'test123'));
        $user->setJoined(new DateTime());

        $manager->persist($user);

        $manager->flush();
    }
}
