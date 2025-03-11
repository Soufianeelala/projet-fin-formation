<?php

// namespace App\Form;

// use Symfony\Component\Form\AbstractType;
// use Symfony\Component\Form\FormBuilderInterface;
// use Symfony\Component\Form\Extension\Core\Type\FileType;
// use Symfony\Component\OptionsResolver\OptionsResolver;
// use App\Entity\Image;
// use Symfony\Component\Validator\Constraints\File;

// class ImageType extends AbstractType
// {
//     public function buildForm(FormBuilderInterface $builder, array $options): void
//     {
//         $builder
//             ->add('file', FileType::class, [
//                 'label' => 'Télécharger une image',
//                 'mapped' => false, // Empêche Symfony de lier ce champ à l'entité
//                 'required' => true,
//                 'constraints' => [
//                     new File([
//                         'maxSize' => '5M',
//                         'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
//                         'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG, PNG, WebP).',
//                     ])
//                 ],
//             ]);
//     }

//     public function configureOptions(OptionsResolver $resolver): void
//     {
//         $resolver->setDefaults([
//             'data_class' => Image::class,
//         ]);
//     }
// }
