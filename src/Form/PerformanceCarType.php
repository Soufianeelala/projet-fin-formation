<?php

// src/Form/PerformanceCarType.php

namespace App\Form;

use App\Entity\PerformanceCar;
use App\Entity\PerformanceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PerformanceCarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $performanceTypes = $options['performance_types']; // Les types de performance à afficher
    
        foreach ($performanceTypes as $performanceType) {
            // Case à cocher pour chaque PerformanceType
            $builder->add('performanceType_' . $performanceType->getId(), CheckboxType::class, [
                'label' => $performanceType->getNom(),
                'required' => false,
                'mapped' => false, // Ne pas mapper directement cette case à cocher sur l'entité
            ]);
    
            // Champ de texte pour saisir la valeur, visible seulement si la case est cochée
            $builder->add('valeur_' . $performanceType->getId(), TextType::class, [
                'label' => 'Valeur pour ' . $performanceType->getNom(),
                'required' => false,
                'mapped' => false, // Ne pas mapper directement cette valeur à l'entité PerformanceCar
            ]);
        }
    
        // Ajouter le bouton de soumission
        $builder->add('submit', SubmitType::class, [
            'label' => 'Ajouter Performance',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PerformanceCar::class, // Modifiez cela pour que le formulaire lie directement les valeurs à PerformanceCar
            'performance_types' => null, // Types de performance injectés
        ]);
    }
}
