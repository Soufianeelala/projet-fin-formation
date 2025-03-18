<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarRepository::class)]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $marque = null;

    #[ORM\Column(length: 255)]
    private ?string $modele = null;

    #[ORM\Column]
    private ?int $annee = null;

    #[ORM\ManyToOne(inversedBy: 'cars')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'car', orphanRemoval: true)]
    private Collection $images;

    /**
     * @var Collection<int, PerformanceType>
     */
    #[ORM\ManyToMany(targetEntity: PerformanceType::class, mappedBy: 'cars', cascade: ['persist'])]
    private Collection $performanceTypes;

    /**
     * @var Collection<int, MotorisationType>
     */
    #[ORM\ManyToMany(targetEntity: MotorisationType::class, mappedBy: 'cars', cascade: ['persist'])]
    private Collection $motorisationTypes;

    // #[ORM\ManyToOne(inversedBy: 'cars')]
    // private ?User $user = null;
    #[ORM\ManyToOne(inversedBy: 'cars', cascade: ['remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;
    private ?User $owner = null;



    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->performanceTypes = new ArrayCollection();
        $this->motorisationTypes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(string $marque): static
    {
        $this->marque = $marque;

        return $this;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(string $modele): static
    {
        $this->modele = $modele;

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): static
    {
        $this->annee = $annee;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setCar($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getCar() === $this) {
                $image->setCar(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PerformanceType>
     */
    public function getPerformanceTypes(): Collection
    {
        return $this->performanceTypes;
    }

    public function addPerformanceType(PerformanceType $performanceType): static
    {
        if (!$this->performanceTypes->contains($performanceType)) {
            $this->performanceTypes->add($performanceType);
            $performanceType->addCar($this);
        }

        return $this;
    }

    public function removePerformanceType(PerformanceType $performanceType): static
    {
        if ($this->performanceTypes->removeElement($performanceType)) {
            $performanceType->removeCar($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, MotorisationType>
     */
    public function getMotorisationTypes(): Collection
    {
        return $this->motorisationTypes;
    }

    public function addMotorisationType(MotorisationType $motorisationType): static
    {
        if (!$this->motorisationTypes->contains($motorisationType)) {
            $this->motorisationTypes->add($motorisationType);
            $motorisationType->addCar($this);
        }

        return $this;
    }

    public function removeMotorisationType(MotorisationType $motorisationType): static
    {
        if ($this->motorisationTypes->removeElement($motorisationType)) {
            $motorisationType->removeCar($this);
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
    public function getOwner(): ?User
{
    return $this->owner;
}

public function setOwner(?User $owner): static
{
    $this->owner = $owner;
    return $this;
}

}
