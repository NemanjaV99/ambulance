<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Location;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;

class LocationFixtures extends Fixture
{
    private $locationsFile;
    
    public function __construct(string $projectDir)
    {
        // First load the array of location data
        $dataDirs = [$projectDir . '/config/data'];
        $fileLocator = new FileLocator($dataDirs);
        $this->locationsFile = $fileLocator->locate('location.yaml', null, true);
    }

    public function load(ObjectManager $manager)
    {
        $locations = Yaml::parseFile($this->locationsFile);

        foreach ($locations as $region => $regionCities) {

              foreach ($regionCities as $city) {

                  $location = new Location();
                  $location->setCity($city);
                  $location->setRegion($region);

                  $manager->persist($location);
              }
        }

        $this->setReference('location.test', $location);

        $manager->flush();
    }
}
