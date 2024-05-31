<?php

// src/Service/AlertService.php

namespace App\Service;

use App\Entity\Product;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use App\Repository\ShelfRepository;

class AlertService
{
    private $basketRepository;
    private $productRepository;
    private $shelfRepository;

    public function __construct(BasketRepository $basketRepository, ProductRepository $productRepository, ShelfRepository $shelfRepository)
    {
        $this->basketRepository = $basketRepository;
        $this->productRepository = $productRepository;
        $this->shelfRepository = $shelfRepository;
    }

    public function getIncidendiaBasket($baskets): array
    {
        $basketsIncidence = [];
        foreach ($baskets as $basket) {
            if (true == $basket->isIncidencia()) {
                $basketsIncidence[] = $basket;
            }
        }

        return $basketsIncidence;
    }

    public function getIncidendiaShelves(array $basketsIncidence): array
    {
        $shelves = [];
        foreach ($basketsIncidence as $basketIncidence) {
            $shelfId = $basketIncidence->getIdShelfBasket();
            $shelf = $this->shelfRepository->find($shelfId);
            $shelves[] = $shelf;
        }

        return $shelves;
    }

    public function getIncidendiaProduct($basket): ?Product
    {
        $idProduct = $basket->getIdBasketProduct();
        $product = $this->productRepository->find($idProduct);

        return $product;
    }
}
