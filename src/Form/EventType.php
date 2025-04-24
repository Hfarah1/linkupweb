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


use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;

use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
        ->add('titre', TextType::class, [
            'required' => false 
        ])
        ->add('description', TextareaType::class, [
            'required' => false // ⛔️ Cela empêche le navigateur d'appliquer la validation HTML
        ]) // TextareaType est déjà correctement importé

        ->add('dateDebut', DateType::class, [
            'widget' => 'single_text',           // Pour utiliser le datepicker JS
            'input' => 'datetime',
            'html5' => false,                    // Désactive le calendrier HTML5
            'required' => false, 
            'empty_data' => null, 
            'invalid_message' => 'La date de début est obligatoire.',            
            'attr' => [
                'class' => 'form-control datepicker-custom',
                'autocomplete' => 'off',
                'required' => false, 
                  
                'placeholder' => 'Choisissez la date de début'
            ],
           
        ])
        

            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'html5' => false,
                'required' => false,
                'empty_data' => null, 
                'invalid_message' => 'La date de fin est obligatoire.',  
                'attr' => [
                    'class' => 'form-control datepicker-custom',
                    'autocomplete' => 'off',
                    'required' => false, 
                    'placeholder' => 'Date de fin'
                ]
            ])
            
            ->add('imagePath', FileType::class, [
                'label' => 'Image',
                'mapped' => false, // Ne pas lier ce champ à une propriété de l'entité
                'required' => false,
            ]);
            $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();
        
                $dateDebut = $data->getDateDebut(); // ou getDate_debut()
                $dateFin = $data->getDateFin();
        
                if ($dateDebut && $dateFin && $dateDebut > $dateFin) {
                    $form->get('dateDebut')->addError(new FormError('La date de début doit être antérieure à la date de fin.'));
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class, // Lier le formulaire à l'entité Event
        ]);
    }
}
