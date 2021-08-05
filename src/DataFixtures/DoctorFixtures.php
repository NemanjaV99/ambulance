<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Doctor;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\TypeDoctorFixtures;

class DoctorFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [
            UserFixtures::class,
            TypeDoctorFixtures::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        $doctor = new Doctor();
        $doctor->setFirstName('Marko');
        $doctor->setLastName('Markovic');

        // Retrieve the test doctor user account reference and link it with this doctor 
        $user = $this->getReference('user.test_doctor');
        $doctor->setUser($user);

        // Retrieve the type doctor and link it with this test doctor
        $type = $this->getReference('type_doctor.test');
        $doctor->setType($type);

        $this->setReference('doctor.examination_1', $doctor);

        $manager->persist($doctor);

        $doctor = new Doctor();
        $doctor->setFirstName('Stefan');
        $doctor->setLastName('Stefanovic');

        // Retrieve the test doctor user account reference and link it with this doctor 
        $user = $this->getReference('user.test_doctor_2');
        $doctor->setUser($user);

        // Retrieve the type doctor and link it with this test doctor
        $type = $this->getReference('type_doctor.test');
        $doctor->setType($type);

        $this->setReference('doctor.examination_2', $doctor);

        $manager->persist($doctor);

        $manager->flush();
    }
}
