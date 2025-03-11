<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\Image;
use App\Form\CarType;
use App\Form\CarImagesType;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/car')]
final class CarController extends AbstractController
{
    #[Route(name: 'app_car_index', methods: ['GET'])]
    public function index(CarRepository $carRepository): Response
    {
        return $this->render('car/index.html.twig', [
            'cars' => $carRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_car_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $car = new Car();
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($car);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_car_add_images', ['id' => $car->getId()]);
        }
    
        return $this->render('car/new.html.twig', [
            'car' => $car,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/add-images', name: 'app_car_add_images', methods: ['GET', 'POST'])]
    public function addImages(Request $request, Car $car, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CarImagesType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFiles = $form->get('files')->getData() ?? [];

            if (count($imageFiles) > 3) {
                $this->addFlash('error', 'Vous ne pouvez ajouter que 3 images maximum.');
                return $this->redirectToRoute('app_car_add_images', ['id' => $car->getId()]);
            }

            foreach ($imageFiles as $file) {
                if ($file instanceof UploadedFile) {
                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                    $file->move('uploads/cars', $fileName);

                    $image = new Image();
                    $image->setFilePath('uploads/cars/' . $fileName);
                    $image->setCar($car);

                    $entityManager->persist($image);
                }
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_car_index');
        }

        return $this->render('car/add_images.html.twig', [
            'car' => $car,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_car_show', methods: ['GET'])]
    public function show(Car $car): Response
    {
        return $this->render('car/show.html.twig', [
            'car' => $car,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_car_edit', methods: ['GET', 'POST'])]
    public function edit(Car $car, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_car_edit_images', ['id' => $car->getId()]);
        }

        return $this->render('car/edit.html.twig', [
            'form' => $form->createView(),
            'car' => $car,
        ]);
    }

    #[Route('/{id}/edit-images', name: 'app_car_edit_images', methods: ['GET', 'POST'])]
    public function editImages(Request $request, Car $car, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CarImagesType::class);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFiles = $form->get('files')->getData() ?? [];
            $existingImagesCount = count($car->getImages()); // ✅ Nombre d'images déjà enregistrées
            $totalImagesAfterUpload = $existingImagesCount + count($imageFiles);
    
            if ($totalImagesAfterUpload > 3) { // ✅ Change la limite selon ton besoin
                $this->addFlash('error', 'Une voiture ne peut pas avoir plus de 10 images.');
                return $this->redirectToRoute('app_car_edit_images', ['id' => $car->getId()]);
            }
    
            foreach ($imageFiles as $file) {
                if ($file instanceof UploadedFile) {
                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                    $file->move('uploads/cars', $fileName);
    
                    $image = new Image();
                    $image->setFilePath('uploads/cars/' . $fileName);
                    $image->setCar($car);
    
                    $entityManager->persist($image);
                }
            }
    
            $entityManager->flush();
            return $this->redirectToRoute('app_car_edit_images', ['id' => $car->getId()]);
        }
    
        return $this->render('car/edit_images.html.twig', [
            'car' => $car,
            'form' => $form->createView(),
        ]);
    }
    

    #[Route('/image/{id}/delete', name: 'app_car_delete_image', methods: ['POST'])]
    public function deleteImage(Image $image, EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $request->request->get('_token'))) {
            $imagePath = $this->getParameter('kernel.project_dir') . '/public/' . $image->getFilePath();

            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $entityManager->remove($image);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_car_edit_images', ['id' => $image->getCar()->getId()]);
    }

    #[Route('/{id}', name: 'app_car_delete', methods: ['POST'])]
    public function delete(Request $request, Car $car, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $car->getId(), $request->request->get('_token'))) {
            foreach ($car->getImages() as $image) {
                $imagePath = $this->getParameter('kernel.project_dir') . '/public/' . $image->getFilePath();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $entityManager->remove($image);
            }

            $entityManager->remove($car);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_car_index');
    }
}
