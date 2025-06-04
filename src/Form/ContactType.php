<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add("name", TextType::class, [
            "label" => false,
            "attr"=>["placeholder" => "Votre nom"]
        ])
        ->add("email", EmailType::class, [
            "label" => false,
            "attr"=>["placeholder" => "Votre adresse mail"]
        ])
        ->add("phone", NumberType::class, [
            "label" => false,
            "attr"=>["placeholder" => "Votre numero de téléphone"]
        ])
        ->add("message", TextareaType::class, [
            "label" => false,
            "attr"=>["placeholder" => "Entrez votre message"]
        ])
        ->add("submit", SubmitType::class, [
            "label" => "Envoyer"
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }}