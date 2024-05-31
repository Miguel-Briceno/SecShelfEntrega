<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PickingShelfType;
use App\Repository\UserRepository;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use App\Service\PickingService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminController extends AbstractController
{
    private $authenticationUtils;
    private $basketRepository;
    private $entityManager;    
    private $productRepository;
    private $userRepository;

    public function __construct(AuthenticationUtils $authenticationUtils, BasketRepository $basketRepository, EntityManagerInterface $entityManager, ProductRepository $productRepository, UserRepository $userRepository)
    {            
        $this->authenticationUtils = $authenticationUtils;
        $this->basketRepository = $basketRepository;
        $this->entityManager = $entityManager;   
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
    }

    #[Route("/admin", name: "app_admin")]
    public function showShelfPick(Request $request): Response
    {
        //authenticated user
        $user = $this->getViewUser();

        //form to select shelf 
        $form = $this->createForm(PickingShelfType::class);
        $form->handleRequest($request);

        //if form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {

            $idShelf = $form->getData()->getIdShelfBasket()->getId();

            $this->addFlash(
               'success',
               'Shelf selected successfully!'
            );     
            
            return $this->redirectToRoute('app_admin_basket', ['id' => $idShelf],  );
        }

        return $this->render('admin/index.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route("/admin/basket_pick", name: "app_admin_basket")]
    public function showBasketPick(Request $request): Response
    {   
        //authenticated user
        $user = $this->getViewUser();
        //id shelf by method get
        $idShelf = $request->get('id');
        //get baskets by shelf
        $baskets = $this->basketRepository->findBy(['idShelfBasket' => $idShelf]);
        
        return $this->render('admin/baskets.html.twig', [
            'user' => $user,
            'baskets' => $baskets,
        ]);
    }

    #[Route("/admin/product_pick", name: "app_admin_product")]
    public function showProductPick(Request $request,PickingService $ps): Response
    {
        //authenticated user
        $user = $this->getViewUser();
        //id basket by method get
        $idBasket = $request->get('idBasket');
        //get basket by basket id
        $basket = $this->basketRepository->findOneBy(['id' => $idBasket]);       
        //get product by basket
        $product = $ps->getProductoByBasket($basket);        
        
        $this->addFlash(
            'success',
            'Basket selected successfully!'
         );         
        
        return $this->render('admin/basket_detail.html.twig', [
            'user' => $user,
            'product' => $product,            
            'basket' => $basket,
        ]);
    }

    #[Route("/admin/pick_up", name: "app_admin_pick_up")]
    public function pickUp(Request $request, PickingService $ps, LoggerInterface $logger): Response
    {   
        //authenticated user
        $user = $this->getViewUser();
    
        // id basket by method get
        $idBasket = $request->get('idBasket');

        // pickProduct by method get
        $pickProduct = $request->get('pickProduct');
        $pickProduct = filter_var($pickProduct, FILTER_VALIDATE_BOOLEAN);
      
        // lowest weight        
        $lowestWeight = $this->productRepository->findProductWithLowestWeight()->getProductWeight();         
        $basket = $ps->getBasketByIdBasket($idBasket); // basket
        $shelfId = $basket->getIdShelfBasket()->getId(); // shelf id
      
        $product = $ps->getProductoByBasket($basket); // product
        $weightTotal = $basket->getTotalWeight(); // total weight

        // if the basket is empty
        if (!($weightTotal >= $lowestWeight)) {
            $this->addFlash('error', 'The basket is empty!');
            return $this->redirectToRoute('app_admin');
        }
         
            
        if($pickProduct){
            
            // set product by check
            $ps->setProductByCheck($basket, $this->entityManager, $product);
            
            // sensor weight
            $weightSensor = $ps->getWeightSensor($basket, $product);
            $weightTotal = $basket->getTotalWeight();
            if($ps->macth($weightSensor, $weightTotal)){
                $this->addFlash(
                    'success',
                    'Product picked up successfully!'
                );
                
                return $this->render('/admin/result.html.twig', [  
                    'user' => $user,                                             
                    'idBasket' => $idBasket,
                    'weightSensor' => $weightSensor,
                    'weightTotal' => $weightTotal,                        
                ]  );
            }else{
                $this->addFlash(
                    'success',
                    'The weight is not correct!'
                );
                return $this->redirectToRoute('app_admin_pickUp',  );
            }
                
        }else{              
 
            // set product without check
            $weightSensor = $ps->setWeightSensortWithoutCheck($product, $basket);
            
            $weightTotal = $basket->getTotalWeight();
            // incidencia
            $basket->setIncidencia(true);
            $this->entityManager->persist($basket);
            $this->entityManager->flush();
            $logger->error('The product has not been check!', ['idBasket' => $idBasket,'shelfId' => $shelfId] );
            $this->addFlash(
                    'error',
                    'The product has not been check!'
                );
            return $this->redirectToRoute('app_alert_mailer', ['id'=>$idBasket], );
                                  
        }
    }
    #[Route("/admin/product_pick/reset", name: "app_reset")]
    public function reset( ): Response
    {   
        try {
            // reset incidencia
            $this->basketRepository->setIncidenciaToFalseForAllBaskets();
            $this->addFlash(
                'success',
                'Reset successfully!'
            );
             return $this->redirectToRoute('app_admin');
        } catch (\Throwable $th) {
            $this->addFlash(
                'error',
                'Something wrong happen!'
            );
            return $this->redirectToRoute('app_admin');
        }             
    }
   
    // user object
    private function getViewUser(): ?User
    {
        $userEmail = $this->authenticationUtils->getLastUsername();
        $user = $this->userRepository->findOneBy(['email' => $userEmail]);
        return $user;
    }
    
}
