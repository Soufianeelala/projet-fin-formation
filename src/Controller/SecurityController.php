<?php
// src/Controller/SecurityController.php

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

        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Intercepté par le firewall.');
    }

    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function request(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $submittedToken = $request->request->get('_token');

            if ($this->isCsrfTokenValid('forgot_password', $submittedToken)) {
                $user = $userRepository->findOneBy(['email' => $email]);

                if ($user) {
                    $token = Uuid::v4()->toRfc4122();
                    $user->setResetToken($token);
                    $user->setResetTokenExpiresAt(new \DateTimeImmutable('+1 hour'));
                    $entityManager->flush();

                    $resetLink = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                    $email = (new Email())
                        ->from('noreply@yourdomain.com')
                        ->to($user->getEmail())
                        ->subject('Réinitialisation de votre mot de passe')
                        ->text("Cliquez sur ce lien pour réinitialiser votre mot de passe : $resetLink");

                    $mailer->send($email);

                    $this->addFlash('success', 'Un email de réinitialisation a été envoyé.');
                    return $this->redirectToRoute('app_login');
                }

                $this->addFlash('error', 'Aucun utilisateur trouvé pour cet email.');
            }
        }

        return $this->render('security/forgot_password.html.twig');
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function resetPassword(
        Request $request,
        string $token,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository
    ): Response {
        $user = $userRepository->findOneBy(['resetToken' => $token]);

        if (!$user || $user->getResetTokenExpiresAt() < new \DateTimeImmutable()) {
            $this->addFlash('danger', 'Ce lien est invalide ou a expiré.');
            return $this->redirectToRoute('app_forgot_password');
        }

        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);

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
