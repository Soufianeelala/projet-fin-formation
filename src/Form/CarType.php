<?php

// src/Form/CarType.php
namespace App\Form;

use App\Entity\Car;
use App\Entity\Categorie;
use App\Entity\PerformanceCar;
use App\Entity\PerformanceType;
use App\Entity\MotorisationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use App\Form\PerformanceCarType;
use Symfony\Component\Form\Extension\Core\Type\TextType;



class CarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('marque')
            ->add('modele')
            ->add('matricule', TextType::class, [
                'label' => 'Matricule',
                'required' => true,
            ])
            ->add('annee')
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
            ])
            // // Ajout des performances : multiple = true permet de choisir plusieurs performances
            // ->add('performanceTypes', EntityType::class, [
            //     'class' => PerformanceType::class,
            //     'choice_label' => 'nom',
            //     'multiple' => true,       // Permet la sélection multiple
            //     'expanded' => true,       // Affiche les cases à cocher
            //     'label' => 'Sélectionnez les performances',
            //     'required' => true,       // S'assurer qu'il y a une sélection
            //     'mapped' => false //pour lier les performances à l'entité Car
            // ])
            
            // // Champ pour les valeurs des performances
            // ->add('performanceCars', CollectionType::class, [
            //     'entry_type' => PerformanceCarType::class,
            //     'allow_add' => true,
            //     'by_reference' => false,
            //     'label' => 'Valeurs des performances',
            // ])
            
           
            // Ajout des motorisations : similaire à performanceTypes
            ->add('motorisationTypes', EntityType::class, [
                'class' => MotorisationType::class,
                'choice_label' => 'nom',
                'multiple' => true,       // Permet la sélection multiple
                'expanded' => true,       // Affiche les cases à cocher
                'label' => 'Sélectionnez les motorisations',
                'by_reference' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Car::class,  // L'entité associée
        ]);
    }
}
