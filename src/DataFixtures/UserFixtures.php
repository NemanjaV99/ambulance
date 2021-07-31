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
        // Admin test account
        $user = new User();
        $user->setUsername('admin');
        $user->setPassword($this->passHasher->hashPassword($user, 'admin123'));
        $user->setRoles(['ROLE_ADMIN']);
        $user->setJoined(new DateTime());
        $manager->persist($user);

        // Doctor test account
        $user = new User();
        $user->setUsername('doctor');
        $user->setPassword($this->passHasher->hashPassword($user, 'doctest123'));
        $user->setRoles(['ROLE_DOCTOR']);
        $user->setJoined(new DateTime());

        // This will set a reference that we can later use in the DoctorFixtures when creating test doctor
        $this->setReference('user.test_doctor', $user);

        $manager->persist($user);

        $user = new User();
        $user->setUsername('doctor2');
        $user->setPassword($this->passHasher->hashPassword($user, 'doctor123'));
        $user->setRoles(['ROLE_DOCTOR']);
        $user->setJoined(new DateTime());

        // This will set a reference that we can later use in the DoctorFixtures when creating test doctor
        $this->setReference('user.test_doctor_2', $user);

        $manager->persist($user);

        // Counter test account
        $user = new User();
        $user->setUsername('counter');
        $user->setPassword($this->passHasher->hashPassword($user, 'counter123'));
        $user->setRoles(['ROLE_COUNTER']);
        $user->setJoined(new DateTime());
        $manager->persist($user);

        $manager->flush();
    }
}
