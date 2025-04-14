<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType; 
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType; 
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class) // Utilisation correcte de TextType
            ->add('description', TextareaType::class) // TextareaType est déjà correctement importé

            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'attr' => [
                    'class' => 'form-control datepicker-custom',
                    'autocomplete' => 'off',
                    'placeholder' => 'Date de début'
                ]
            ])

            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'attr' => [
                    'class' => 'form-control datepicker-custom',
                    'placeholder' => 'Date de fin'
                ]
            ])

            ->add('imagePath', FileType::class, [
                'label' => 'Image',
                'mapped' => false, // Ne pas lier ce champ à une propriété de l'entité
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class, // Lier le formulaire à l'entité Event
        ]);
    }
}
