<?php

namespace App\Controller;

use App\Entity\Shelf;
use App\Form\ShelfType;
use App\Repository\ShelfRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;



#[Route('/admin/shelf')]
class ShelfController extends AbstractController
{
    #[Route('/', name: 'app_shelf_index', methods: ['GET'])]
    public function index(ShelfRepository $shelfRepository): Response
    {
        return $this->render('shelf/index.html.twig', [
            'shelves' => $shelfRepository->findAll(),
        ]);
    }
    
    #[Route('/new', name: 'app_shelf_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $shelf = new Shelf();
        $form = $this->createForm(ShelfType::class, $shelf);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $shelf->setDateTime(new \DateTime());
            $this->addFlash(
               'success',
               'Shelf created successfully!'
            );
            $entityManager->persist($shelf);
            $entityManager->flush();

            return $this->redirectToRoute('app_shelf_index', [], Response::HTTP_SEE_OTHER);
        }
       
        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true) as $error) {
                $this->addFlash(
                    'error',
                    $error->getOrigin()->getName() . ': ' . $error->getMessage()
                );
            }
            return $this->redirectToRoute('app_shelf_new', [], Response::HTTP_SEE_OTHER);
        }

        
        return $this->render('shelf/new.html.twig', [
            'shelf' => $shelf,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_shelf_show', methods: ['GET'])]
    public function show(Shelf $shelf): Response
    {
        return $this->render('shelf/show.html.twig', [
            'shelf' => $shelf,
        ]);
    }
    
    #[Route('/{id}/edit', name: 'app_shelf_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Shelf $shelf, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ShelfType::class, $shelf);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash(
                'success',
                'Shelf edit successfully!'
             );
            $entityManager->flush();
            return $this->redirectToRoute('app_shelf_index', [], Response::HTTP_SEE_OTHER);
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true) as $error) {
                $this->addFlash(
                    'error',
                    $error->getOrigin()->getName() . ': ' . $error->getMessage()
                );
            }
            return $this->redirectToRoute('app_shelf_edit', ['id' => $shelf->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('shelf/edit.html.twig', [
            'shelf' => $shelf,
            'form' => $form,
        ]);
    }
    
    #[Route('/{id}', name: 'app_shelf_delete', methods: ['POST'])]
    public function delete(Request $request, Shelf $shelf, EntityManagerInterface $entityManager): Response
    {
        try{
            if ($this->isCsrfTokenValid('delete'.$shelf->getId(), $request->getPayload()->get('_token'))) {
                
                $entityManager->remove($shelf);
                $entityManager->flush();
                $this->addFlash(
                    'success',
                    'Shelf delete successfully!'
                );
            }
        }catch (ForeignKeyConstraintViolationException $e) {
            $this->addFlash(
                'error',
                'Cannot delete shelf because it is being used by a basket.'
            );
        }
    
        return $this->redirectToRoute('app_shelf_index', [], Response::HTTP_SEE_OTHER);
    }
}
