<?php

namespace App\Controller;

use App\Entity\Basket;

use App\Form\BasketType;
use App\Repository\UserRepository;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\BasketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/admin/basket')]
class BasketController extends AbstractController
{
    private $userRepository;
    private $authenticationUtils;
    private $entityManager;
    

    public function __construct(UserRepository $userRepository, AuthenticationUtils $authenticationUtils, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->authenticationUtils = $authenticationUtils;
        $this->entityManager = $entityManager;
       
    }

    #[Route('/', name: 'app_basket_index', methods: ['GET'])]
    public function index(BasketRepository $basketRepository): Response
    {
        return $this->render('basket/index.html.twig', [
            'baskets' => $basketRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_basket_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $basket = new Basket();
        $form = $this->createForm(BasketType::class, $basket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getViewUser($this->authenticationUtils, $this->userRepository);
            $basket->setIdUserBasket($user);
            $basket->setIncidencia("0");
            $basket->setDateTimeBasket(new \DateTime());
            $basket->setTotalWeight($this->getWeigtTotal( $basket)); //se carga el peso total de la cesta
            $this->addFlash(
                'success',
                'Basket created successfully!'
             );
            $this->entityManager->persist($basket);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_basket_index', [], Response::HTTP_SEE_OTHER);
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true) as $error) {
                $this->addFlash(
                    'error',
                    $error->getOrigin()->getName() . ': ' . $error->getMessage()
                );
            }
            return $this->redirectToRoute('app_basket_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('basket/new.html.twig', [
            'basket' => $basket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_basket_show', methods: ['GET'])]
    public function show(Basket $basket): Response
    {
        return $this->render('basket/show.html.twig', [
            'basket' => $basket,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_basket_edit', methods: ['GET', 'POST'])]
    public function edit(Basket $basket, Request $request): Response
    {
        $form = $this->createForm(BasketType::class, $basket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash(
                'success',
                'Basket edit successfully!'
             );

            return $this->redirectToRoute('app_basket_index', ['id' => $basket->getId()], Response::HTTP_SEE_OTHER);
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true) as $error) {
                $this->addFlash(
                    'error',
                    $error->getOrigin()->getName() . ': ' . $error->getMessage()
                );
            }
            return $this->redirectToRoute('app_basket_edit', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('basket/edit.html.twig', [
            'basket' => $basket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_basket_delete', methods: ['POST'])]
    public function delete(Basket $basket, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$basket->getId(), $request->getPayload()->get('_token'))) {
            if ($basket->getIdBasketProduct() !== null) {
                $this->addFlash(
                    'error',
                    'Cannot delete basket because it has a product associated.'
                );
            } else {
               
                    $this->entityManager->remove($basket);
                    $this->entityManager->flush();
                    $this->addFlash(
                        'success',
                        'Basket delete successfully!'
                    );             
            }
        }

        return $this->redirectToRoute('app_basket_index', [], Response::HTTP_SEE_OTHER);
    }    

    private function getViewUser($authentication, $userRepo)
    {
        $userEmail = $authentication->getLastUsername();
        $user = $userRepo->findOneBy(['email' => $userEmail]);
        return $user;
    }
    public function getWeigtTotal($basket): float
    {        
        $product = $basket->getIdBasketProduct();                 
        $weightTotal = $basket->getNumProduct() * $product->getProductWeight(); 
                
        return $weightTotal;        
    }
}
