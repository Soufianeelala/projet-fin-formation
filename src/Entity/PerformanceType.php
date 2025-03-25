<?php

namespace App\Entity;

use App\Repository\PerformanceTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PerformanceTypeRepository::class)]
class PerformanceType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var Collection<int, PerformanceCar>
     */
    #[ORM\OneToMany(targetEntity: PerformanceCar::class, mappedBy: 'performanceType', orphanRemoval: true)]
    private Collection $performanceCars;

    public function __construct()
    {
        $this->performanceCars = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    /**
     * @return Collection<int, PerformanceCar>
     */
    public function getPerformanceCars(): Collection
    {
        return $this->performanceCars;
    }

    public function addPerformanceCar(PerformanceCar $performanceCar): static
    {
        if (!$this->performanceCars->contains($performanceCar)) {
            $this->performanceCars->add($performanceCar);
            $performanceCar->setPerformanceType($this);
        }
        return $this;
    }

    public function removePerformanceCar(PerformanceCar $performanceCar): static
    {
        if ($this->performanceCars->removeElement($performanceCar)) {
            if ($performanceCar->getPerformanceType() === $this) {
                $performanceCar->setPerformanceType(null);
            }
        }
        return $this;
    }
}
