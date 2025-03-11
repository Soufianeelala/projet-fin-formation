<?php

namespace App\Form;

use App\Entity\Car;
use App\Entity\PerformanceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PerformanceTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
                //             ->add('cars', EntityType::class, [
                //                 'class' => Car::class,
                // 'choice_label' => 'id',
                // 'multiple' => true,
                //             ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PerformanceType::class,
        ]);
    }
}
