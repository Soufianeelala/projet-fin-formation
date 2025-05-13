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

    
    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'car', orphanRemoval: true)]
    private Collection $images;

    /**
     * @var Collection<int, MotorisationType>
     */
    #[ORM\ManyToMany(targetEntity: MotorisationType::class, mappedBy: 'cars', cascade: ['persist'])]
    private Collection $motorisationTypes;

    #[ORM\ManyToOne(inversedBy: 'cars')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    private ?User $owner = null;

    /**
     * @var Collection<int, PerformanceCar>
     */
    #[ORM\OneToMany(mappedBy: 'car', targetEntity: PerformanceCar::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $performanceCars;

    #[ORM\Column(length: 255 , unique: true)]
    private ?string $matricule = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;


    #[ORM\ManyToMany(targetEntity: Categorie::class, inversedBy: 'cars')]
    private Collection $categories;
    
    
    
    public function getCategories(): Collection
    {
        return $this->categories;
    }
    
    public function addCategory(Categorie $categorie): static
    {
        if (!$this->categories->contains($categorie)) {
            $this->categories->add($categorie);
        }
        return $this;
    }
    
    public function removeCategory(Categorie $categorie): static
    {
        $this->categories->removeElement($categorie);
        return $this;
    }


    // Constructeur combiné
    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->motorisationTypes = new ArrayCollection();
        $this->performanceCars = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->categories = new ArrayCollection();

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
            if ($image->getCar() === $this) {
                $image->setCar(null);
            }
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
            $performanceCar->setCar($this);
        }
        return $this;
    }

    public function removePerformanceCar(PerformanceCar $performanceCar): static
    {
        if ($this->performanceCars->removeElement($performanceCar)) {
            if ($performanceCar->getCar() === $this) {
                $performanceCar->setCar(null);
            }
        }
        return $this;
    }

    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    public function setMatricule(string $matricule): static
    {
        $this->matricule = $matricule;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
