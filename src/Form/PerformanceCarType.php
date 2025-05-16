<?php

namespace App\Form;

use App\Entity\PerformanceCar;
use App\Entity\PerformanceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class PerformanceCarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $performanceTypes = $options['performance_types'];
        $existingData = $options['existing_data'] ?? [];

        foreach ($performanceTypes as $performanceType) {
            $id = $performanceType->getId();
            $label = $performanceType->getNom();
            
            // Trouver la valeur existante pour ce type de performance
            $existingValue = null;
            $isChecked = false;
            
            foreach ($existingData as $perf) {
                if ($perf->getPerformanceType()->getId() === $id) {
                    $existingValue = $perf->getValeur();
                    $isChecked = true;
                    break;
                }
            }

            $builder->add("performanceType_$id", CheckboxType::class, [
                'label' => $label,
                'required' => false,
                'mapped' => false,
                'data' => $isChecked,
                'attr' => [
                    'class' => 'performance-checkbox',
                    'data-id' => $id
                ]
            ]);

            $builder->add("valeur_$id", TextType::class, [
                'label' => false,
                'required' => false,
                'mapped' => false,
                'data' => $existingValue,
                'attr' => [
                    'placeholder' => 'Valeur',
                    'class' => 'performance-value',
                    'data-id' => $id
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null, // Important pour éviter l'erreur
            'performance_types' => [],
            'existing_data' => [], // Tableau d'objets PerformanceCar existants
        ]);
    }
}