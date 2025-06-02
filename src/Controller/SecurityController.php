<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Uid\Uuid;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Cette méthode peut rester vide - elle est interceptée par le firewall de Symfony.');
    }

    #[Route('/forgot-password', name: 'app_forgot_password')]
     public function request(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
{
    // création du formulaire pour réinitialiser le mot de passe
    $form=$this->createForm(ResetPasswordRequestFormType::class);
    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){
        // récupère le mail du formulaire
        $email = $form->get("email")->getData();
        $user = $userRepository->findOneBy(["email"=>$email]);
        // vérifie si l'utilisateur avec cet email existe
        if ($user) {
            // token généré pour 1h
            $token = Uuid::v4()->toRfc4122();
            $user->setResetToken($token);
            $user->setResetTokenExpiresAt((new \DateTime())->modify("+1 hour"));
            $entityManager->flush();
            // lien de réinitialisation généré avec le token
            $resetLink = $this->generateUrl("app_reset_password", ['token'=>$token], UrlGeneratorInterface:: ABSOLUTE_URL);
            // prépare et envoi l'email
            $email=(new Email())
            ->from("noreply@yourdomain.com")
            ->to($user->getEmail())
            ->subject ("Réinitialisation de votre mot de passe")
            ->text("Voici votre lien de réinitialisation : $resetLink");
            $mailer->send($email);

            $this->addFlash("success", "un email de réinitialisation vous a été envoyé");
            // redirige l'utilisateur vers la page Se connecter
            return $this->redirectToRoute("app_login");
        }
        $this->addFlash("error", "Aucun utilisateur trouvé pour cet email.");
    }
    return $this->render('security/reset_password.html.twig', [
         'reset-password-form' => $form->createView(),
      ]);
}
    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function resetPassword(
        Request $request,
        string $token,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,UserRepository $userRepository
    ): Response {
        $user = $userRepository->findOneBy(['resetToken' => $token]);

        if (!$user || $user->getResetTokenExpiresAt() < new \DateTimeImmutable()) {
            $this->addFlash('danger', 'Ce lien est invalide ou a expiré.');
            return $this->redirectToRoute('app_forgot_password');
        }

        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('plainPassword')->getData();
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $user->setPassword($hashedPassword);
            $user->setResetToken(null);
            $user->setResetTokenExpiresAt(null);
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe mis à jour avec succès.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }
}
