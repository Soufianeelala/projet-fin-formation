<?php

namespace App\Form;

use App\Entity\Commentaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;  // Si tu veux un champ de date
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Ajout du champ commentaire
            ->add('commentaire', TextareaType::class, [
                'label' => 'Votre commentaire',
                'attr' => ['rows' => 4],
           
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaire::class, // L'entité associée au formulaire
        ]);
    }
}
