<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
//     #[Route('/', name: 'app_home')]
//     public function index(): Response
//     {
//         return $this->render('base.html.twig');
//     }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Redirige vers la page d'accueil si l'utilisateur est déjà connecté
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // Récupère l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        // Récupère le dernier email saisi
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): Response
    {
        // Symfony gère automatiquement la déconnexion
        return $this->redirectToRoute('app_home');
    }
    #[Route('/forgot-password', name: 'app_forgot_password')]
public function forgotPassword(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
{
    if ($request->isMethod('POST')) {
        $email = $request->request->get('email');
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user) {
            // Générer un token de réinitialisation
            $token = bin2hex(random_bytes(32));
            $user->setResetToken($token);
            $entityManager->flush();

            // Envoyer l'email avec le lien
            $email = (new TemplatedEmail())
                ->from('noreply@tonsite.com')
                ->to($user->getEmail())
                ->subject('Réinitialisation de votre mot de passe')
                ->htmlTemplate('security/reset_password.html.twig')
                ->context([
                    'token' => $token,
                ]);

            $mailer->send($email);
        }

        $this->addFlash('success', 'Un email de réinitialisation a été envoyé.');
        return $this->redirectToRoute('app_login');
    }

    return $this->render('security/forgot_password.html.twig');
}
#[Route('/reset-password/{token}', name: 'app_reset_password')]
public function resetPassword(Request $request, string $token, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
{
    $user = $entityManager->getRepository(User::class)->findOneBy(['resetToken' => $token]);

    if (!$user) {
        throw $this->createNotFoundException('Token invalide');
    }

    if ($request->isMethod('POST')) {
        $newPassword = $request->request->get('password');
        $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);
        $user->setResetToken(null);
        $entityManager->flush();

        $this->addFlash('success', 'Mot de passe mis à jour ! Connectez-vous.');
        return $this->redirectToRoute('app_login');
    }

    return $this->render('security/reset_password.html.twig', ['token' => $token]);
}


}
