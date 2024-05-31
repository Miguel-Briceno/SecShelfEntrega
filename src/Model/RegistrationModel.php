<?php
// src/Service/AlertService.php
namespace App\Model;

use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mime\Address;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;



class RegistrationModel extends AbstractController
{
    private $slugger;
    private $userPasswordHasher; 
    private $emailVerifier;  

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $userPasswordHasher, EmailVerifier $emailVerifier)
    {
        $this->slugger = $slugger;  
        $this->userPasswordHasher = $userPasswordHasher;
        $this->emailVerifier = $emailVerifier;
       
    }   
    // file upload
    public function fileUpload($file, $user): string
    {
        if ($file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
            try {
                $file->move(
                    $this->getParameter('files_directory'),
                    $newFilename
                );
                
            } catch (FileException $e) {
                throw new BadRequestHttpException('Error al cargar la imagen');
            }
            return $newFilename;  
        }
    }
    // encode password
    function encodePassword($user, $userPassword): string
    {       
            $userPasswordHasher = $this->userPasswordHasher->hashPassword(
                $user,
                $userPassword
            );
            return $userPasswordHasher;        
    }

    //send email registration
    public function sendEmailRegistration($user): void
    {
        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address('check@ss.com', 'Sec Shelf'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')                    
        );
    }
   
}
