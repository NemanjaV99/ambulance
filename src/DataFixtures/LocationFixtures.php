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

        $persistedLocations = [];

        foreach ($locations as $city) {

            $location = new Location();
            $location->setCity($city);
        
            $manager->persist($location);
            $persistedLocations[] = $location;
        }

        // Randomly select one of the persisted locations
        // We could remove the element after retrieving it from the persisted array to make sure the same location is never selected twice, but for now this is okay
        $this->setReference('location.test_1', $persistedLocations[rand(0, count($persistedLocations) - 1)]);
        $this->setReference('location.test_2', $persistedLocations[rand(0, count($persistedLocations) - 1)]);

        $manager->flush();
    }
}
