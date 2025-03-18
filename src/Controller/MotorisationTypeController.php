<?php

namespace App\Controller;

use App\Entity\MotorisationType;
use App\Form\MotorisationTypeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\MotorisationTypeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/motorisation/type')]
final class MotorisationTypeController extends AbstractController{
    #[Route(name: 'app_motorisation_type_index', methods: ['GET'])]
    public function index(MotorisationTypeRepository $motorisationTypeRepository): Response
    {
        return $this->render('motorisation_type/index.html.twig', [
            'motorisation_types' => $motorisationTypeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_motorisation_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $motorisationType = new MotorisationType();
        $form = $this->createForm(MotorisationTypeType::class, $motorisationType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($motorisationType);
            $entityManager->flush();

            return $this->redirectToRoute('app_motorisation_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('motorisation_type/new.html.twig', [
            'motorisation_type' => $motorisationType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_motorisation_type_show', methods: ['GET'])]
    public function show(MotorisationType $motorisationType): Response
    {
        return $this->render('motorisation_type/show.html.twig', [
            'motorisation_type' => $motorisationType,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_motorisation_type_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, MotorisationType $motorisationType, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MotorisationTypeType::class, $motorisationType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_motorisation_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('motorisation_type/edit.html.twig', [
            'motorisation_type' => $motorisationType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_motorisation_type_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, MotorisationType $motorisationType, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$motorisationType->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($motorisationType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_motorisation_type_index', [], Response::HTTP_SEE_OTHER);
    }
}
