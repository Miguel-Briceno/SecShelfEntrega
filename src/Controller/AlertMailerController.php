<?php

namespace App\Controller;

use App\Repository\BasketRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class AlertMailerController extends AbstractController
{
    #[Route('/admin/mailer', name: 'app_alert_mailer')]
    public function sendEmail(MailerInterface $mailer, Request $request, BasketRepository $basketRepository, LoggerInterface $logger): RedirectResponse
    {
        $basketId = $request->get('id');
        $basket = $basketRepository->findOneBy(['id' => $basketId]);
        $shelfId = $basket->getIdShelfBasket()->getId();

        $email = (new TemplatedEmail())
        ->from('SecShelfo@example.com')
        ->to('Gerente@example.com')
        ->cc('EncargadoTurno@example.com')
        ->priority(Email::PRIORITY_HIGH)
        ->subject('Alert Shelves!')
        ->htmlTemplate('alert_mailer/mail.html.twig')
        ->context([
            'basketId' => $basketId,
            'shelfId' => $shelfId,
        ]);

        try {
            $mailer->send($email);
            $this->addFlash('error_email', 'Correo electrónico enviado correctamente.');

            return $this->redirectToRoute('app_view');
        } catch (TransportExceptionInterface $e) {
            // error message or try to resend the message
            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                // Log error
                $logger->alert('Error enviando el correo: '.$e->getMessage());

                // Show error message
                $this->addFlash('error_mail', 'No se pudo enviar el correo electrónico. Por favor, inténtelo de nuevo más tarde.');

                return $this->redirectToRoute('app_view');
            }
        } catch (\Exception $e) {
            // Log error
            $logger->alert('Error inesperado: '.$e->getMessage());

            // Show error message
            $this->addFlash('error_general', 'Ha ocurrido un error inesperado. Por favor, inténtelo de nuevo más tarde.');

            return $this->redirectToRoute('app_view');
        }

        return $this->redirectToRoute('app_view');
    }
}
