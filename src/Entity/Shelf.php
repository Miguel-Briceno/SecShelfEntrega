<?php

namespace App\Entity;

use App\Repository\ShelfRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShelfRepository::class)]
class Shelf
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $basketCapacity = null;

    #[ORM\Column]
    private ?float $kgCapacity = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateTime = null;

    /**
     * @var Collection<int, Basket>
     */
    #[ORM\OneToMany(targetEntity: Basket::class, mappedBy: 'idShelfBasket')]
    private Collection $idShelfBasket;

    public function __construct()
    {
        $this->idShelfBasket = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBasketCapacity(): ?int
    {
        return $this->basketCapacity;
    }

    public function setBasketCapacity(int $basketCapacity): static
    {
        $this->basketCapacity = $basketCapacity;

        return $this;
    }

    public function getKgCapacity(): ?float
    {
        return $this->kgCapacity;
    }

    public function setKgCapacity(float $kgCapacity): static
    {
        $this->kgCapacity = $kgCapacity;

        return $this;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTimeInterface $dateTime): static
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * @return Collection<int, Basket>
     */
    public function getIdShelfBasket(): Collection
    {
        return $this->idShelfBasket;
    }

    public function addIdShelfBasket(Basket $idShelfBasket): static
    {
        if (!$this->idShelfBasket->contains($idShelfBasket)) {
            $this->idShelfBasket->add($idShelfBasket);
            $idShelfBasket->setIdShelfBasket($this);
        }

        return $this;
    }

    public function removeIdShelfBasket(Basket $idShelfBasket): static
    {
        if ($this->idShelfBasket->removeElement($idShelfBasket)) {
            // set the owning side to null (unless already changed)
            if ($idShelfBasket->getIdShelfBasket() === $this) {
                $idShelfBasket->setIdShelfBasket(null);
            }
        }

        return $this;
    }

    // En la clase Shelf
    public function __toString()
    {
        return (string) $this->getId(); // Suponiendo que getId() devuelve el ID de la estantería como una cadena
    }
}
