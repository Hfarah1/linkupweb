<?php

namespace App\Form;

use App\Entity\Rencontre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class RencontreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_utilisateur', HiddenType::class, [
                'required' => true
            ])
            ->add('titre', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un titre']),
                ],
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer une description']),
                ],
            ])
            ->add('categorie_rencontre', ChoiceType::class, [
                'choices' => [
                    'Sport' => 'Sport',
                    'Gastronomie' => 'Gastronomie',
                    'Culture' => 'Culture',
                    'Aventure' => 'Aventure',
                    'Éducation' => 'Éducation',
                    'Autre' => 'Autre'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner une catégorie']),
                ],
            ])
            ->add('lieu', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un lieu']),
                ],
            ])
            ->add('date_rencontre', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'data' => new \DateTime(),
                'constraints' => [
                    new NotBlank([
                        'message' => 'La date de rencontre ne peut pas être vide'
                    ]),
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image (JPG, PNG)',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPG, PNG, GIF)',
                    ])
                ],
            ])
            ->add('image_name', HiddenType::class, [
                'required' => false,
            ])
            ->add('tags', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'sport, plein air, loisirs...'
                ]
            ])
            ->add('places_dispo', IntegerType::class, [
                'required' => false,
                'attr' => [
                    'min' => 1,
                    'placeholder' => 'Nombre de places disponibles'
                ],
            ])
            ->add('budget', MoneyType::class, [
                'required' => false,
                'currency' => 'EUR',
                'divisor' => 1,
                'scale' => 2,
                'attr' => [
                    'placeholder' => '0.00'
                ]
            ])
            ->add('bio', TextareaType::class, [
                'required' => false
            ])
            ->add('statut', HiddenType::class, [
                'required' => false,
                'data' => 'actif'
            ])
            ->add('age_min', IntegerType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un âge minimum']),
                ],
                'attr' => [
                    'min' => 18,
                    'max' => 99
                ],
            ])
            ->add('age_max', IntegerType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un âge maximum']),
                ],
                'attr' => [
                    'min' => 18,
                    'max' => 99
                ],
            ])
            ->add('distance_max', IntegerType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer une distance maximum']),
                ],
                'attr' => [
                    'min' => 1,
                    'max' => 1000
                ],
            ])
            ->add('genre_recherche', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'Tous' => 'Tous',
                    'Hommes' => 'Hommes',
                    'Femmes' => 'Femmes',
                    'Non-binaire' => 'Non-binaire'
                ],
            ])
            ->add('niveau_relation', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'Amitié' => 'Amitié',
                    'Relation sérieuse' => 'Relation sérieuse',
                    'Casual' => 'Casual',
                    'Réseautage' => 'Réseautage',
                    'Professionnel' => 'Professionnel'
                ],
            ])
            ->add('created_at', HiddenType::class, [
                'required' => false,
                'mapped' => false,
            ])
            ->add('latitude', HiddenType::class, [
                'required' => false
            ])
            ->add('longitude', HiddenType::class, [
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rencontre::class,
        ]);
    }
}
