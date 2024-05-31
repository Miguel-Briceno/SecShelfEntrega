<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Model\RegistrationModel;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private $emailVerifier;
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager,EmailVerifier $emailVerifier) 
    {
        $this->emailVerifier = $emailVerifier;
        $this->entityManager = $entityManager;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request,  RegistrationModel $registrationModel): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);        

        if ($form->isSubmitted() && $form->isValid()) {            
           
            $file = $form->get('photo')->getData(); // get the file from the form            
            $user->setPhoto($registrationModel->fileUpload($file, $user));// upload the file

            $plainPassword = $form->get('plainPassword')->getData();// get the plain password           
            $user->setPassword($registrationModel->encodePassword($user, $plainPassword)); // encode the plain password           
            
            $this->entityManager->persist($user);// persist the user
            $this->entityManager->flush();       // flush the entity manager            
            
            $registrationModel->sendEmailRegistration($user);   // Enviar el email usando el servicio de envío de correos   
                 
            $this->addFlash('success', 'You should verify your email address.'); // success message

            return $this->redirectToRoute('app_register');
            
        }
        if ($form->isSubmitted() && !$form->isValid()) {// Verificar errores específicos
            
            foreach ($form->getErrors(true) as $error) {
                $this->addFlash(
                    'error',
                    $error->getOrigin()->getName() . ': ' . $error->getMessage()
                );
            }
        }

        return $this->render('registration/register.html.twig', [
            
            'registrationForm' => $form->createView(),            
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {   
        $id = $request->get('id'); // retrieve the user id from the url
        $user = $userRepository->find($id); // search the user by id
        
        if (null === $id) {// Verify the user id exists and is not null
            $this->addFlash('error', 'Id null');
            return $this->redirectToRoute('app_register');
        }        
 
        // validate email confirmation link, sets User::isVerified=true and persists
        try {                   
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}
