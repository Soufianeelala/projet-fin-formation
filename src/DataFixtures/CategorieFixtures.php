<?php
// src/DataFixtures/CategorieFixtures.php
namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategorieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            'Citadine',
            'Berline',
            'SUV',
            'Crossover',
            'Break',
            'Coupé',
            'Cabriolet',
            'Monospace',
            'Pick-up',
            '4x4',
            'Hybride',
            'Électrique',
            'Sportive',
            'Utilitaire',
            'Minibus',
        ];

        foreach ($categories as $nom) {
            $categorie = new Categorie();
            $categorie->setNom($nom); 
            $manager->persist($categorie);
        }

        $manager->flush();
    }
}