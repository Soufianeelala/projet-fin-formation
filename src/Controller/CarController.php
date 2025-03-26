<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\Image;
use App\Form\CarType;
use App\Form\CarImagesType;
use App\Entity\PerformanceCar;
use App\Entity\PerformanceType;
use App\Entity\MotorisationType;
use App\Form\PerformanceCarType;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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







 // Page 1 : Ajouter une voiture
 #[Route('/new', name: 'app_car_new', methods: ['GET', 'POST'])]
 public function new(Request $request, EntityManagerInterface $entityManager): Response
 {
     $car = new Car();
     $form = $this->createForm(CarType::class, $car);
     $form->handleRequest($request);

    
     if ($form->isSubmitted() && $form->isValid()) {
        // Associer l'utilisateur connecté à la voiture
        $user = $this->getUser();
        $car->setUser($user); // Assure-toi que l'entité Car a bien un champ user

        // Récupérer les types de motorisation depuis le formulaire
        foreach ($car->getMotorisationTypes() as $motorisationType) {
            $existingMotorisation = $entityManager->getRepository(MotorisationType::class)
                ->findOneBy(['nom' => $motorisationType->getNom()]);

            if (!$existingMotorisation) {
                $entityManager->persist($motorisationType);
            } else {
                $car->removeMotorisationType($motorisationType);
                $car->addMotorisationType($existingMotorisation);
            }
        }

         
         $entityManager->persist($car);
         $entityManager->flush();

         return $this->redirectToRoute('app_car_add_performance', ['id' => $car->getId()]);
        }

     return $this->render('car/new.html.twig', [
         'car' => $car,
         'form' => $form->createView(),
     ]);
 }


// Page 2 : Ajouter des performances
// src/Controller/CarController.php

// #[Route('/{id}/add-performance', name: 'app_car_add_performance')]
// public function addPerformance(Car $car, Request $request, EntityManagerInterface $entityManager): Response
// {
//     // Récupérer tous les types de performance disponibles
//     $performanceTypes = $entityManager->getRepository(PerformanceType::class)->findAll();

//     // Créer le formulaire
//     $form = $this->createForm(PerformanceCarType::class, null, [
//         'performance_types' => $performanceTypes,
//     ]);

//     $form->handleRequest($request);

//     if ($form->isSubmitted() && $form->isValid()) {
//         // Traiter les types de performance et les valeurs saisies
//         foreach ($performanceTypes as $performanceType) {
//             $checkboxField = 'performanceType_' . $performanceType->getId();
//             $valueField = 'valeur' . $performanceType->getId();

//             // Vérifier si la case est cochée
//             if ($form->has($checkboxField) && $form->get($checkboxField)->getData()) {
//                 $value = $form->has($valueField) ? $form->get($valueField)->getData() : null;

//                 if (!empty($value)) {
//                     // Créer un nouvel objet PerformanceCar
//                     $performanceCar = new PerformanceCar();
//                     $performanceCar->setCar($car);
//                     $performanceCar->setPerformanceType($performanceType);
//                     $performanceCar->setValeur($value);

//                     $entityManager->persist($performanceCar);
//                 }
//             }
//         }

//         // Sauvegarder les performances
//         $entityManager->flush();

//         $this->addFlash('success', 'Performances ajoutées avec succès !');
//         return $this->redirectToRoute('app_car_add_images', ['id' => $car->getId()]);
//     }

//     return $this->render('car/add_performance.html.twig', [
//         'car' => $car,
//         'form' => $form->createView(),
//     ]);
// }

#[Route('/{id}/add-performance', name: 'app_car_add_performance')]
public function addPerformance(Car $car, Request $request, EntityManagerInterface $entityManager): Response
{
    // Récupérer les types de performance
    $performanceTypes = $entityManager->getRepository(PerformanceType::class)->findAll();

    // Créer le formulaire
    $form = $this->createForm(PerformanceCarType::class, null, [
        'performance_types' => $performanceTypes,
    ]);

    $form->handleRequest($request);

    // Vérification si le formulaire est soumis et valide
    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();  // Récupérer les données envoyées par le formulaire
        
        foreach ($performanceTypes as $performanceType) {
            $performanceId = $performanceType->getId();
            
            // Vérifier si la performance a été sélectionnée
            if ($form->get('performanceType_' . $performanceId)->getData()) {
                // Si la performance est sélectionnée, on récupère la valeur associée
                $value = $form->get('valeur_' . $performanceId)->getData();
                
                if ($value) {
                    $performanceCar = new PerformanceCar();
                    $performanceCar->setCar($car);
                    $performanceCar->setPerformanceType($performanceType);
                    $performanceCar->setValeur((string) $value);  // Conversion explicite en chaîne
                    
                    $entityManager->persist($performanceCar);
                }
            }
        }

        // Sauvegarder les performances dans la base de données
        $entityManager->flush();
        
        $this->addFlash('success', 'Performances ajoutées avec succès !');
        return $this->redirectToRoute('app_car_add_images', ['id' => $car->getId()]);
    }

    // Si le formulaire n'est pas soumis ou valide, on affiche les erreurs
    if (!$form->isSubmitted()) {
        dump('Formulaire non soumis');
        dump($request->request->all()); // Affiche tout ce qui est dans la requête POST
    }

    return $this->render('car/add_performance.html.twig', [
        'car' => $car,
        'form' => $form->createView(),
        'performance_types' => $performanceTypes,
    ]);
}




 // Page 3 : Ajouter des images
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
         return $this->redirectToRoute('app_car_show', ['id' => $car->getId()]);
        }

     return $this->render('car/add_images.html.twig', [
         'car' => $car,
         'form' => $form->createView(),
     ]);
 }

 

 // Afficher une voiture
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
    public function delete(Request $request, Car $car, EntityManagerInterface $entityManager, Security $security): Response
    {
        $user = $security->getUser();
    
        if ($user !== $car->getUser() && !$security->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException("Vous n'avez pas le droit de supprimer cette voiture.");
        }
    
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
   
    #[Route('/{id}/edit-details', name: 'app_car_edit_details', methods: ['GET', 'POST'])]
    public function editPerformance(Car $car, Request $request, EntityManagerInterface $entityManager): Response
    {
        $performanceTypes = $entityManager->getRepository(PerformanceType::class)->findAll();
        $existingPerformances = $entityManager->getRepository(PerformanceCar::class)->findBy(['car' => $car]);

        // Créer les données initiales
        $data = [];
        foreach ($performanceTypes as $performanceType) {
            $existingPerformance = array_filter($existingPerformances, fn($pc) => $pc->getPerformanceType()->getId() === $performanceType->getId());
            $data['performanceType_' . $performanceType->getId()] = !empty($existingPerformance);
            $data['valeur_' . $performanceType->getId()] = $existingPerformance ? reset($existingPerformance)->getValeur() : null;
        }

        // Créer et gérer le formulaire
        $form = $this->createForm(PerformanceCarType::class, null, [
            'performance_types' => $performanceTypes,
            'data_class' => null, // Pour éviter l'erreur liée à l'attente d'une entité
            'data' => $data
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($performanceTypes as $performanceType) {
                $performanceId = $performanceType->getId();
                $isChecked = $form->get('performanceType_' . $performanceId)->getData();
                $value = $form->get('valeur_' . $performanceId)->getData();

                $existingPerformance = array_filter($existingPerformances, fn($pc) => $pc->getPerformanceType()->getId() === $performanceId);

                if ($isChecked) {
                    if (empty($existingPerformance)) {
                        $performanceCar = new PerformanceCar();
                        $performanceCar->setCar($car);
                        $performanceCar->setPerformanceType($performanceType);
                        $performanceCar->setValeur((string) $value);
                        $entityManager->persist($performanceCar);
                    } else {
                        $performanceCar = reset($existingPerformance);
                        $performanceCar->setValeur((string) $value);
                    }
                } else if (!empty($existingPerformance)) {
                    $entityManager->remove(reset($existingPerformance));
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Performances mises à jour avec succès !');
            return $this->redirectToRoute('app_car_show', ['id' => $car->getId()]);
        }

        return $this->render('car/edit_performance.html.twig', [
            'car' => $car,
            'form' => $form->createView(),
            'performance_types' => $performanceTypes,
        ]);
    }
} 