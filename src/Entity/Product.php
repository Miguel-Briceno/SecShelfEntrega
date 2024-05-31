<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $productBrand = null;

    #[ORM\Column(length: 255)]
    private ?string $productName = null;

    #[ORM\Column]
    private ?float $productWeight = null;

    #[ORM\Column(length: 255)]
    private ?string $productModel = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateTimeProduct = null;

    #[ORM\ManyToOne(inversedBy: 'idUserProduct')]
    private ?User $IdUserProduct = null;

    /**
     * @var Collection<int, Basket>
     */
    #[ORM\OneToMany(targetEntity: Basket::class, mappedBy: 'idBasketProduct')]
    private Collection $baskets;

    public function __construct()
    {
        $this->baskets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductBrand(): ?string
    {
        return $this->productBrand;
    }

    public function setProductBrand(string $productBrand): static
    {
        $this->productBrand = $productBrand;

        return $this;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): static
    {
        $this->productName = $productName;

        return $this;
    }

    public function getProductWeight(): ?float
    {
        return $this->productWeight;
    }

    public function setProductWeight(float $productWeight): static
    {
        $this->productWeight = $productWeight;

        return $this;
    }

    public function getProductModel(): ?string
    {
        return $this->productModel;
    }

    public function setProductModel(string $productModel): static
    {
        $this->productModel = $productModel;

        return $this;
    }

    public function getDateTimeProduct(): ?\DateTimeInterface
    {
        return $this->dateTimeProduct;
    }

    public function setDateTimeProduct(\DateTimeInterface $dateTimeProduct): static
    {
        $this->dateTimeProduct = $dateTimeProduct;

        return $this;
    }

    public function getIdUserProduct(): ?User
    {
        return $this->IdUserProduct;
    }

    public function setIdUserProduct(?User $IdUserProduct): static
    {
        $this->IdUserProduct = $IdUserProduct;

        return $this;
    }

    /**
     * @return Collection<int, Basket>
     */
    public function getBaskets(): Collection
    {
        return $this->baskets;
    }

    public function addBasket(Basket $basket): static
    {
        if (!$this->baskets->contains($basket)) {
            $this->baskets->add($basket);
            $basket->setIdBasketProduct($this);
        }

        return $this;
    }

    public function removeBasket(Basket $basket): static
    {
        if ($this->baskets->removeElement($basket)) {
            // set the owning side to null (unless already changed)
            if ($basket->getIdBasketProduct() === $this) {
                $basket->setIdBasketProduct(null);
            }
        }

        return $this;
    }

    // En la clase Product
    public function __toString()
    {
        return $this->getProductName(); // Suponiendo que `getName()` devuelve el nombre del producto
    }
}
