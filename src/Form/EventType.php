<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control']
            ])
            ->add('date_debut', DateTimeType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('date_fin', DateTimeType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ]);

        if ($options['type'] === 'virtual') {
            $builder
                ->add('duree', TextType::class, [
                    'label' => 'Durée (en minutes)',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Ex: 60'
                    ]
                ])
                ->add('acces', ChoiceType::class, [
                    'label' => 'Type d\'accès',
                    'choices' => [
                        'Public' => 'public',
                        'Privé' => 'private'
                    ],
                    'attr' => ['class' => 'form-control']
                ])
                ->add('code', TextType::class, [
                    'label' => 'Code d\'accès',
                    'required' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Laissez vide pour générer automatiquement'
                    ]
                ]);
        }

        $builder
            ->add('image_path', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG ou PNG)',
                    ])
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('places', IntegerType::class, [
                'required' => false,
                'attr' => [
                    'min' => 1,
                    'placeholder' => 'Nombre de places disponibles'
                ]
            ]);

        // Ajouter les champs spécifiques aux événements présentiels
        if ($options['type'] === 'physical') {
            $builder
                ->add('lieu', TextType::class, [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'L\'adresse est obligatoire pour un événement présentiel'
                        ])
                    ],
                    'attr' => [
                        'placeholder' => 'Adresse de l\'événement'
                    ]
                ])
                ->add('ville', TextType::class, [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'La ville est obligatoire pour un événement présentiel'
                        ])
                    ],
                    'attr' => [
                        'placeholder' => 'Ville'
                    ]
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'type' => 'virtual' // Par défaut, on considère que c'est un événement virtuel
        ]);

        $resolver->setAllowedValues('type', ['virtual', 'physical']);
    }
}
