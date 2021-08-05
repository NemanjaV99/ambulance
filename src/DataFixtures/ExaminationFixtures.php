<?php

namespace App\DataFixtures;

use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Examination;
use App\DataFixtures\PatientFixtures;
use App\DataFixtures\DoctorFixtures;

class ExaminationFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [
            PatientFixtures::class,
            DoctorFixtures::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        // For performed examinations set date to current, for not performed set a future date
        
        // Performed
        $examination = new Examination();
        $examination->setPatient($this->getReference('patient.examination_1'));
        $examination->setDoctor($this->getReference('doctor.examination_2'));
        $examination->setDate(new DateTime());
        $examination->setDiagnosis('Lorem ipsum dolor sit amet consectetur, adipisicing elit. Laborum accusamus autem veniam.');
        $examination->setPerformed(true);

        $manager->persist($examination);

        // Not performed
        $examination = new Examination();
        $examination->setPatient($this->getReference('patient.examination_2'));
        $examination->setDoctor($this->getReference('doctor.examination_1'));
        $date = new DateTime();
        $date->setTimestamp(mt_rand(time(), strtotime('31 December 2021')));
        $examination->setDate($date);
        $examination->setPerformed(false);

        $manager->persist($examination);

        $manager->flush();
    }
}
