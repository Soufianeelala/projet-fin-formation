<?php

namespace App\Entity;

use App\Repository\PerformanceCarRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PerformanceCarRepository::class)]
class PerformanceCar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'performanceCars')]
    #[ORM\JoinColumn(nullable: false , onDelete: 'CASCADE')]
    private ?Car $car = null;

    #[ORM\ManyToOne(inversedBy: 'performanceCars')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PerformanceType $performanceType = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $valeur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): static
    {
        $this->car = $car;

        return $this;
    }

    public function getPerformanceType(): ?PerformanceType
    {
        return $this->performanceType;
    }

    public function setPerformanceType(?PerformanceType $performanceType): static
    {
        $this->performanceType = $performanceType;

        return $this;
    }

    public function getValeur(): ?string
    {
        return $this->valeur;
    }

    public function setValeur(string $valeur): static
    {
        $this->valeur = $valeur;

        return $this;
    }
}
