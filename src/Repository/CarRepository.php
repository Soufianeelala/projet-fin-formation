<?php

namespace App\Repository;

use App\Entity\Car;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Car>
 */
class CarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Car::class);
    }

    /**
     * Fonction pour récupérer les voitures en fonction du filtre.
     * @param string|null $filter Le filtre à appliquer.
     * @param int|null $year L'année pour filtrer les voitures (si spécifié).
     * @param string|null $marque La marque pour filtrer les voitures (si spécifiée).
     * @return Car[] Retourne un tableau d'objets Car
     */
    public function findAllWithFilter(?string $filter, ?int $year = null, ?string $marque = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.images', 'i')
            ->addSelect('i');
    
        // Filtre par année
        if ($year !== null) {
            $qb->andWhere('c.annee = :year')
               ->setParameter('year', $year);
        }
    
        // Filtre par marque
        if ($marque !== null && $marque !== '') {
            $qb->andWhere('c.marque = :marque')
               ->setParameter('marque', $marque);
        }
    
        // Tri des résultats
        switch ($filter) {
            case 'date_desc':
                $qb->orderBy('c.annee', 'DESC');
                break;
            case 'date_asc':
                $qb->orderBy('c.annee', 'ASC');
                break;
            default:
                // Tri par défaut selon l'ID
                $qb->orderBy('c.id', 'DESC');
                break;
        }
    
        return $qb->getQuery()->getResult();
    }

    /**
     * Récupérer les marques distinctes des voitures.
     * @return string[] Retourne un tableau avec les marques distinctes sous forme de chaîne de caractères.
     */
    public function getDistinctMarques(): array
    {
        // Récupérer les marques distinctes sous forme de tableau simple
        return $this->createQueryBuilder('c')
            ->select('DISTINCT c.marque')  // Sélectionner les marques distinctes
            ->getQuery()
            ->getResult();

        }


        // src/Repository/CarRepository.php
public function findLastCars(int $limit = 4): array
{
    return $this->createQueryBuilder('c')
        ->orderBy('c.createdAt', 'DESC')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
}

}
