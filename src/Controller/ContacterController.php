<?php
// src/Controller/ContacterController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ContacterController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, MailerInterface $mailer)
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

       

        if ($form->isSubmitted() && $form->isValid()) {
            //  dd($form->getData());

    $data = $form->getData();

    $email = (new Email())
        ->from('souf07091991@gmail.com') 
        ->to('soufianeelalami1991@gmail.com')
        ->replyTo($data['email'])
        ->subject('Nouveau message de contact')
        ->text(
            "Nom: {$data['name']}\n" .
            "Email: {$data['email']}\n\n" .
            "Sujet : {$data['sujet']}\n\n" .
            "Message:\n{$data['message']}"
        );

    try {
        $mailer->send($email);
    } catch (\Exception $e) {
        dd('Erreur d’envoi : ' . $e->getMessage());
    }

    $this->addFlash('success', 'Message envoyé avec succès !');
    return $this->redirectToRoute('app_contact');
}

        return $this->render('contacter/contacter.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
