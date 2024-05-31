<?php

namespace App\Entity;

use App\Repository\BasketRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BasketRepository::class)]
class Basket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $totalWeight = null;

    #[ORM\Column]
    private ?int $numProduct = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateTimeBasket = null;

    #[ORM\Column]
    private ?bool $incidencia = null;

    #[ORM\ManyToOne(inversedBy: 'idShelfBasket')]
    private ?Shelf $idShelfBasket = null;

    #[ORM\ManyToOne(inversedBy: 'idUserBasket')]
    private ?User $IdUserBasket = null;

    #[ORM\ManyToOne(inversedBy: 'baskets')]
    private ?Product $idBasketProduct = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalWeight(): ?float
    {
        return $this->totalWeight;
    }

    public function setTotalWeight(float $totalWeight): static
    {
        $this->totalWeight = $totalWeight;

        return $this;
    }

    public function getNumProduct(): ?int
    {
        return $this->numProduct;
    }

    public function setNumProduct(int $numProduct): static
    {
        $this->numProduct = $numProduct;

        return $this;
    }

    public function getDateTimeBasket(): ?\DateTimeInterface
    {
        return $this->dateTimeBasket;
    }

    public function setDateTimeBasket(\DateTimeInterface $dateTimeBasket): static
    {
        $this->dateTimeBasket = $dateTimeBasket;

        return $this;
    }

    public function isIncidencia(): ?bool
    {
        return $this->incidencia;
    }

    public function setIncidencia(bool $incidencia): static
    {
        $this->incidencia = $incidencia;

        return $this;
    }

    public function getIdShelfBasket(): ?Shelf
    {
        return $this->idShelfBasket;
    }

    public function setIdShelfBasket(?Shelf $idShelfBasket): static
    {
        $this->idShelfBasket = $idShelfBasket;

        return $this;
    }

    public function getIdUserBasket(): ?User
    {
        return $this->IdUserBasket;
    }

    public function setIdUserBasket(?User $IdUserBasket): static
    {
        $this->IdUserBasket = $IdUserBasket;

        return $this;
    }

    public function getIdBasketProduct(): ?Product
    {
        return $this->idBasketProduct;
    }

    public function setIdBasketProduct(?Product $idBasketProduct): static
    {
        $this->idBasketProduct = $idBasketProduct;

        return $this;
    }
}
