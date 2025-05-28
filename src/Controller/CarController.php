<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\Image;
use App\Form\CarType;
use App\Entity\Commentaire;
use App\Form\CarImagesType;
use App\Form\CommentaireType;
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
// use Symfony\Component\DependencyInjection\Loader\Configurator\request;



#[Route('/car')]
final class CarController extends AbstractController
{
    #[Route( name: 'app_car_index', methods: ['GET'])]
    public function index(CarRepository $carRepository, Request $request): Response
    {  $filter = $request->query->get('filter');
        $marque = $request->query->get('marque');
        $year = $request->query->get('year'); 
    
        // Récupérer les voitures filtrées
        $cars = $carRepository->findAllWithFilter($filter, $year, $marque);
    
        // Récupérer les marques distinctes pour l'affichage du formulaire
        $marques = $carRepository->getDistinctMarques();
        
        return $this->render('car/index.html.twig', [
            'cars' => $cars,
            'marques' => $marques, 
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
        $user = $this->getUser();
        $car->setUser($user); 

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
    $performanceTypes = $entityManager->getRepository(PerformanceType::class)->findAll();

    $form = $this->createForm(PerformanceCarType::class, new PerformanceCar(), [
        'performance_types' => $performanceTypes,
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
    foreach ($performanceTypes as $performanceType) {
        $id = $performanceType->getId();
        $checkbox = $form->get('performanceType_' . $id)->getData();
        $value = $form->get('valeur_' . $id)->getData();

        if ($checkbox && $value !== null && $value !== '') {
            $performanceCar = new PerformanceCar();
            $performanceCar->setCar($car);
            $performanceCar->setPerformanceType($performanceType);
            $performanceCar->setValeur((string) $value);

            $entityManager->persist($performanceCar);
        }
    }

    $entityManager->flush();
    $this->addFlash('success', 'Performances ajoutées avec succès !');
    return $this->redirectToRoute('app_car_add_images', ['id' => $car->getId()]);
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
    if (count($car->getPerformanceCars()) === 0) {
        $this->addFlash('error', 'Ajoutez d\'abord les performances.');
        return $this->redirectToRoute('app_car_add_performance', ['id' => $car->getId()]);
    }
     $form = $this->createForm(CarImagesType::class);
     $form->handleRequest($request);

     if ($form->isSubmitted() && $form->isValid()) {
         $imageFiles = $form->get('files')->getData() ?? [];

          if (count($imageFiles) === 0) {
            $this->addFlash('error', 'Vous devez ajouter au moins une image.');
            return $this->redirectToRoute('app_car_add_images', ['id' => $car->getId()]);
        }

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

//  **************************partie show et commentaire *******************************************

 #[Route('/{id}', name: 'app_car_show', methods: ['GET', 'POST'])]
public function show(Request $request, Car $car, EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();

    // ---- Ajout d’un commentaire ----
    $commentaire = new Commentaire();
    $commentaire->setCar($car);
    if ($user) {
        $commentaire->setUser($user);
    }

    $commentaireForm = $this->createForm(CommentaireType::class, $commentaire);
    $commentaireForm->handleRequest($request);

    if ($commentaireForm->isSubmitted() && $commentaireForm->isValid()) {
        $commentaire->setCreatedAt(new \DateTimeImmutable());
        $entityManager->persist($commentaire);
        $entityManager->flush();

        return $this->redirectToRoute('app_car_show', ['id' => $car->getId()]);
    }
 
    // ---- Edition d’un commentaire ----
    $editCommentId = $request->query->get('edit'); // récupère ?edit=ID dans l'URL
    $editForm = null;
    $commentToEdit = null;

    if ($editCommentId) {
        $commentToEdit = $entityManager->getRepository(Commentaire::class)->find($editCommentId);

        // Vérifie si le commentaire existe et si l'utilisateur peut le modifier
        if ($commentToEdit && ($commentToEdit->getUser() === $user || $this->isGranted('ROLE_ADMIN'))) {
            $editForm = $this->createForm(CommentaireType::class, $commentToEdit);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $entityManager->flush();
                return $this->redirectToRoute('app_car_show', ['id' => $car->getId()]);
            }
        }
    }

    return $this->render('car/show.html.twig', [
        'car' => $car,
        'commentaireForm' => $commentaireForm->createView(),
        'editForm' => $editForm ? $editForm->createView() : null,
        'editComment' => $commentToEdit,
    ]);
}

 





    #[Route('/{id}/edit', name: 'app_car_edit', methods: ['GET', 'POST'])]
    public function edit(Car $car, Request $request, EntityManagerInterface $entityManager , Security $security): Response
    {
        $user = $security->getUser();

    if ($user !== $car->getUser() && !$security->isGranted('ROLE_ADMIN')) {
        throw $this->createAccessDeniedException("Vous n'avez pas le droit de modifier cette voiture.");
    }
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_car_edit_details', ['id' => $car->getId()]);

        }

        return $this->render('car/edit.html.twig', [
            'form' => $form->createView(),
            'car' => $car,
        ]);
    }

    #[Route('/{id}/edit-images', name: 'app_car_edit_images', methods: ['GET', 'POST'])]
    public function editImages(Request $request, Car $car, EntityManagerInterface $entityManager, Security $security): Response
    {
        $user = $security->getUser();

        if ($user !== $car->getUser() && !$security->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException("Vous n'avez pas le droit de modifier les images de cette voiture.");
        }
    
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

   #[Route('/{id}/delete', name: 'app_car_delete', methods: ['POST'])]
public function delete(Request $request, Car $car, EntityManagerInterface $entityManager, Security $security): Response
{
    $user = $security->getUser();

    if ($user !== $car->getUser() && !$security->isGranted('ROLE_ADMIN')) {
        throw $this->createAccessDeniedException("Vous n'avez pas le droit de supprimer cette voiture.");
    }

    if (!$this->isCsrfTokenValid('delete' . $car->getId(), $request->request->get('_token'))) {
        throw new \Exception('Token CSRF invalide');
    }

    foreach ($car->getImages() as $image) {
        $imagePath = $this->getParameter('kernel.project_dir') . '/public/' . $image->getFilePath();
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        $entityManager->remove($image);
    }

    $entityManager->remove($car);
    $entityManager->flush();

    return $this->redirectToRoute('app_car_index');
}

  #[Route('/{id}/edit-details', name: 'app_car_edit_details')]
public function editPerformance(Car $car, Request $request, EntityManagerInterface $entityManager): Response
{
    $performanceTypes = $entityManager->getRepository(PerformanceType::class)->findAll();
    $existingPerformances = $entityManager->getRepository(PerformanceCar::class)->findBy(['car' => $car]);

    $form = $this->createForm(PerformanceCarType::class, null, [
        'performance_types' => $performanceTypes,
        'existing_data' => $existingPerformances, // Passer les données existantes
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Supprimer les anciennes performances
        foreach ($existingPerformances as $performance) {
            $entityManager->remove($performance);
        }
        
        // Ajouter les nouvelles performances
        foreach ($performanceTypes as $performanceType) {
            $id = $performanceType->getId();
            
            if ($form->get("performanceType_$id")->getData()) {
                $value = $form->get("valeur_$id")->getData();
                
                if ($value) {
                    $performanceCar = new PerformanceCar();
                    $performanceCar->setCar($car)
                                 ->setPerformanceType($performanceType)
                                 ->setValeur((string)$value);
                    $entityManager->persist($performanceCar);
                }
            }
        }
        
        $entityManager->flush();
        $this->addFlash('success', 'Performances mises à jour avec succès !');
            return $this->redirectToRoute('app_car_edit_images', ['id' => $car->getId()]);    }

    return $this->render('car/edit_performance.html.twig', [
        'car' => $car,
        'form' => $form->createView(),
         'performance_types' => $performanceTypes,
        'existing_data' => $existingPerformances,
    ]);
}
} 