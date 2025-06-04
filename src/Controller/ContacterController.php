<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ContacterController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $email = (new Email())
                ->from('souf07091991@gmail.com') 
                ->to('soufianeelalami1991@gmail.com')
                ->subject('Nouveau message de contact')
                ->text(
                    "Nom : {$data['name']}\n" .
                    "Adresse mail : {$data['email']}\n" .
                    "Téléphone : {$data['phone']}\n" .
                    "Message : {$data['message']}"
                );

            $mailer->send($email);

            $this->addFlash('success', 'Votre message a été envoyé avec succès.');

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contacter/contacter.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
