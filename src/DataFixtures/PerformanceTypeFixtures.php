<?php

namespace App\DataFixtures;

use App\Entity\PerformanceType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PerformanceTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $performanceTypes = [
            'Vitesse maximale',
            'Puissance moteur',
            'Couple moteur',
            'Accélération 0-100 km/h',
            'Consommation moyenne',
            'Emissions CO2',
            'Poids total',
            'Cylindrée',
            'Nombre de cylindres',
            'Type de transmission',
            'Longueur',
            'Largeur',
            'Hauteur',
            'Volume coffre',
            'Nombre de portes',
            'Nombre de sièges',
            'Freinage 100-0 km/h',
            'Vitesse maximale en mode électrique',
            'Autonomie électrique',
        ];

        foreach ($performanceTypes as $typeName) {
            $performanceType = new PerformanceType();
            $performanceType->setNom($typeName);
            $manager->persist($performanceType);
        }

        $manager->flush();
    }
}
