<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/admin/product')]
class ProductController extends AbstractController
{
    private $userRepository;
    private $authenticationUtils;

    public function __construct(UserRepository $userRepository, AuthenticationUtils $authenticationUtils)
    {
        $this->userRepository = $userRepository;
        $this->authenticationUtils = $authenticationUtils;
    }

    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getViewUser($this->authenticationUtils, $this->userRepository);
            $product->setIdUserProduct($user);
            $product->setDateTimeProduct(new \DateTime());
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Product created successfully!'
            );

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true) as $error) {
                $this->addFlash(
                    'error',
                    $error->getOrigin()->getName().': '.$error->getMessage()
                );
            }

            return $this->redirectToRoute('app_product_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Product edit successfully!'
            );

            return $this->redirectToRoute('app_product_index', ['id' => $product->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        try {
            if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->get('_token'))) {
                $entityManager->remove($product);
                $entityManager->flush();
                $this->addFlash(
                    'success',
                    'Product delete successfully!'
                );
            }
        } catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash(
                'error',
                'Cannot delete product because it is being used by a basket.'
            );
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }

    private function getViewUser($authentication, $userRepo)
    {
        $userEmail = $authentication->getLastUsername();
        $user = $userRepo->findOneBy(['email' => $userEmail]);

        return $user;
    }
}
