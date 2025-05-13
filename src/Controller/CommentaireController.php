<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentaireController extends AbstractController
{
    #[Route('/car/{id}/commentaire', name: 'app_commentaire_add', methods: ['POST'])]
    public function add(Request $request, Car $car, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $commentaire = new Commentaire();
        $commentaire->setCar($car);
        $commentaire->setUser($this->getUser());
        $commentaire->setCreatedAt(new \DateTimeImmutable());

        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($commentaire);
            $em->flush();

            $this->addFlash('success', 'Commentaire ajouté avec succès !');
        }

        return $this->redirectToRoute('app_car_show', ['id' => $car->getId()]);
    }
    #[Route('/commentaire/{id}/edit', name: 'app_commentaire_edit')]
public function edit(Commentaire $commentaire, Request $request, EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();

    if (!$user || ($user !== $commentaire->getUser() && !$this->isGranted('ROLE_ADMIN'))) {
        throw $this->createAccessDeniedException("Vous n'avez pas le droit de modifier ce commentaire.");
    }

    $form = $this->createForm(CommentaireType::class, $commentaire);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        return $this->redirectToRoute('app_car_show', ['id' => $commentaire->getCar()->getId()]);
    }

    return $this->render('commentaire/edit.html.twig', [
        'commentaireForm' => $form->createView(),
        'commentaire' => $commentaire
    ]);
}
#[Route('/commentaire/{id}/delete', name: 'app_commentaire_delete', methods: ['POST'])]
public function delete(Request $request, Commentaire $commentaire, EntityManagerInterface $em): Response
{
    $user = $this->getUser();

    // Autoriser uniquement l'auteur ou l'admin
    if (!$user || ($commentaire->getUser() !== $user && !$this->isGranted('ROLE_ADMIN'))) {
        throw $this->createAccessDeniedException();
    }

    // Vérifier token CSRF
    if ($this->isCsrfTokenValid('delete' . $commentaire->getId(), $request->request->get('_token'))) {
        $em->remove($commentaire);
        $em->flush();
    }

    return $this->redirectToRoute('app_car_show', [
        'id' => $commentaire->getCar()->getId()
    ]);
}



}
