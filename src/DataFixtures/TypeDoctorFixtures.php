<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\TypeDoctor;

class TypeDoctorFixtures extends Fixture
{
    private $doctorTypes = ['Dermatologist', 'Cardiologist', 'Ophthamologist', 'Allergist', 'Gynecologist', 'Neurologist'];

    public function load(ObjectManager $manager)
    {
        foreach ($this->doctorTypes as $docType) {

            $type = new TypeDoctor();
            $type->setName($docType);

            $manager->persist($type);
        }

        // This will set a reference that we can later use in the DoctorFixtures when creating test doctor
        // The last type will be taken
        $this->setReference('type_doctor.test', $type);

        $manager->flush();
    }
}
