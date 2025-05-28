<?php
// src/DataFixtures/MotorisationTypeFixtures.php
namespace App\DataFixtures;

use App\Entity\MotorisationType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MotorisationTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $motorisations = [
            'Essence',
            'Diesel',
            'Hybride',
            'Électrique',
            'GPL',
            'Hydrogène',
            'Bioéthanol',
        ];

        foreach ($motorisations as $nom) {
            $motorisation = new MotorisationType();
            $motorisation->setNom($nom);
            $manager->persist($motorisation);
        }

        $manager->flush();
    }
}
