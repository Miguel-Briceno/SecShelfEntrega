<?php

namespace App\Service;

use App\Entity\Basket;
use App\Entity\Product;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;

class PickingService
{
    private $basketRepository;
    private $productRepository;

    public function __construct(BasketRepository $basketRepository, ProductRepository $productRepository)
    {
        $this->basketRepository = $basketRepository;
        $this->productRepository = $productRepository;
    }

    public function getBasketByIdBasket($idBasket): ?Basket
    {
        return $this->basketRepository->findOneBy(['id' => $idBasket]);
    }

    public function getProductoByBasket($basket): ?Product
    {
        $idProduct = $basket->getIdBasketProduct();

        return $this->productRepository->findOneBy(['id' => $idProduct]); // product
    }

    public function getWeightSensor($basket, $product): ?float
    {
        $numProduct = $basket->getNumProduct(); // number product
        $productWeight = $product->getProductWeight(); // product weight

        return $weightSensor = $numProduct * $productWeight;
    }

    public function macth($weightSensor, $totalWeightBBDD): ?bool
    {
        $match = true;
        if ($weightSensor != $totalWeightBBDD) {
            $match = false;
        }

        return $match;
    }

    public function setProductByCheck($basket, $em, $product): void
    {
        $numProduct = $basket->getNumProduct(); // number product
        $productWeight = $product->getProductWeight(); // product weight
        $numProductBasket = $numProduct - 1; // number product basket
        $basket->setNumProduct($numProductBasket); // set number product basket
        $basket->setTotalWeight($productWeight * $numProductBasket); // set total weight
        $em->persist($basket);
        $em->flush();
    }

    public function setWeightSensortWithoutCheck($product, $basket): ?float
    {
        $numProduct = $basket->getNumProduct(); // number product
        $productWeight = $product->getProductWeight(); // product weight
        $numProductBasket = $numProduct - 1; // number product basket
        $weightSensor = $productWeight * $numProductBasket;

        return $weightSensor;
    }
}
