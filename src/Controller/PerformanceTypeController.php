<?php

namespace App\Controller;

use App\Entity\PerformanceType;
use App\Form\PerformanceTypeType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PerformanceTypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/performance/type')]
final class PerformanceTypeController extends AbstractController{
    #[Route(name: 'app_performance_type_index', methods: ['GET'])]
    public function index(PerformanceTypeRepository $performanceTypeRepository): Response
    {
        return $this->render('performance_type/index.html.twig', [
            'performance_types' => $performanceTypeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_performance_type_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $performanceType = new PerformanceType();
        $form = $this->createForm(PerformanceTypeType::class, $performanceType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($performanceType);
            $entityManager->flush();

            return $this->redirectToRoute('app_performance_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('performance_type/new.html.twig', [
            'performance_type' => $performanceType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_performance_type_show', methods: ['GET'])]
    public function show(PerformanceType $performanceType): Response
    {
        return $this->render('performance_type/show.html.twig', [
            'performance_type' => $performanceType,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_performance_type_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]  

    public function edit(Request $request, PerformanceType $performanceType, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PerformanceTypeType::class, $performanceType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_performance_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('performance_type/edit.html.twig', [
            'performance_type' => $performanceType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_performance_type_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, PerformanceType $performanceType, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$performanceType->getId(), $request->request->get('_token'))) {
            $entityManager->remove($performanceType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_performance_type_index', [], Response::HTTP_SEE_OTHER);
    }
}
