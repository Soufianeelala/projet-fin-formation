<?php

namespace App\Form;

use App\Entity\Car;
use App\Entity\Categorie;
use App\Entity\MotorisationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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

            // ✅ Relation ManyToMany avec Categorie
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true, // true = checkbox, false = liste déroulante
                'label' => 'Catégories de voiture',
                'attr' => ['class' => 'custom-dropdown'],            ])

            // ✅ Relation ManyToMany avec MotorisationType
            ->add('motorisationTypes', EntityType::class, [
                'class' => MotorisationType::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Sélectionnez les motorisations',
                'by_reference' => false, // Obligatoire pour ManyToMany avec ArrayCollection
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Car::class,
        ]);
    }
}
