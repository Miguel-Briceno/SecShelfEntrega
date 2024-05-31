<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\User;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use App\Repository\ShelfRepository;
use App\Repository\UserRepository;
use App\Service\AlertService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/view')]
class ViewController extends AbstractController
{
    private $authenticationUtils;
    private $basketRepository;
    private $shelfRepository;
    private $userRepository;

    public function __construct(AuthenticationUtils $authenticationUtils, BasketRepository $basketRepository, ShelfRepository $shelfRepository, UserRepository $userRepository)
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->basketRepository = $basketRepository;
        $this->shelfRepository = $shelfRepository;
        $this->userRepository = $userRepository;
    }

    #[Route('/', name: 'app_view')]
    public function index(AlertService $alertService): Response
    {   // get authenticated user
        $user = $this->getViewUser();

        // get all shelves
        $shelves = $this->shelfRepository->findAll();
        // get basketIncidence
        $basketIncidence = $this->basketRepository->findBy(['incidencia' => true]);
        // get shelvesIncidence
        $shelvesIncidence = $alertService->getIncidendiaShelves($basketIncidence);

        return $this->render('view/shelfView.html.twig', [
            'shelves' => $shelves,
            'user' => $user,
            'shelvesIncidence' => $shelvesIncidence,
        ]);
    }

    #[Route('/{id}', name: 'app_view_shelf')]
    public function viewShelf(Request $request, AlertService $alertService): Response
    {   // get authenticated user
        $user = $this->getViewUser();
        // get shelfId
        $shelfId = $request->get('id');
        // get baskets from shelfid
        $baskets = $this->basketRepository->findBy(['idShelfBasket' => $shelfId]);
        // get basketIncidence
        $basketsIncidence = $alertService->getIncidendiaBasket($baskets);

        return $this->render('view/basketView.html.twig', [
            'shelfId' => $shelfId,
            'baskets' => $baskets,
            'basketsIncidence' => $basketsIncidence,
            'user' => $user,
        ]);
    }

    #[Route('/basket/{id}', name: 'app_view_basket')]
    public function viewBasket(Basket $basket, ProductRepository $ProductRepository): Response
    {
        $incidendiaBasket = $basket->isIncidencia();
        $idProduct = $basket->getIdBasketProduct()->getId();
        $product = $ProductRepository->findOneBy(['id' => $idProduct]);
        $user = $this->getViewUser($this->authenticationUtils, $this->userRepository);

        return $this->render('view/productView.html.twig', [
            'product' => $product,
            'user' => $user,
            'numBasket' => $basket->getId(),
            'incidencia' => $incidendiaBasket,
        ]);
    }

    // get authenticated user
    private function getViewUser(): ?User
    {
        $userEmail = $this->authenticationUtils->getLastUsername();
        $user = $this->userRepository->findOneBy(['email' => $userEmail]);

        return $user;
    }
}
