<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\LocationFixtures;
use App\Entity\Patient;

class PatientFixtures extends Fixture implements DependentFixtureInterface
{

    public function getDependencies()
    {
        return [
            LocationFixtures::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        
  
        $patient = new Patient();
        $patient->setFirstName('Pero');
        $patient->setLastName('Peric');
        $patient->setLocation($this->getReference('location.test_1'));
        $patient->setJMBG(1234567890001);
        $manager->persist($patient);

        $patient = new Patient();
        $patient->setFirstName('Luka');
        $patient->setLastName('Jankovic');
        $patient->setLocation($this->getReference('location.test_2'));
        $patient->setJMBG(1342567870980);
        $manager->persist($patient);

        $manager->flush();
    }
}
