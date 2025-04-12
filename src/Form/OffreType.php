<?php

namespace App\Form;

use App\Entity\Offre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class OffreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre de l\'offre*',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Développeur Symfony Senior'
                ],
                'help' => '100 caractères maximum'
            ])
            ->add('gouvernorat', TextType::class, [
                'label' => 'Gouvernorat*',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Tunis ou En Ligne'
                ]
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville*',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Bardo'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description détaillée*',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 6,
                    'placeholder' => 'Décrivez en détail le poste et les missions'
                ],
                'help' => '20 à 2000 caractères'
            ])
            ->add('organisation', TextType::class, [
                'label' => 'Organisation*',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nom de votre entreprise/organisation'
                ]
            ])
            ->add('logoFile', FileType::class, [
                'label' => 'Logo de l\'organisation',
                'mapped' => false, // Important: This field is not mapped to the entity
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/*'
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG, PNG ou WebP)',
                        'maxSizeMessage' => 'La taille maximale autorisée est 2Mo',
                    ])
                ],
                'help' => 'Formats acceptés: JPG, PNG, WebP (max 2Mo)'
            ])
            ->add('type_contrat', ChoiceType::class, [
                'label' => 'Type de contrat*',
                'choices' => [
                    'Bénévolat' => 'Bénévolat',
                    'Mission' => 'Mission',
                    'CDD Événementiel' => 'CDD Événementiel',
                    'CDI Saisonnier' => 'CDI Saisonnier',
                    'Freelance' => 'Freelance',
                    'Contrat' => 'Contrat',
                    'Stage' => 'Stage'
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('categorie', ChoiceType::class, [
                'label' => 'Catégorie*',
                'choices' => [
                    'Marketing & Communication' => 'Marketing & Communication',
                    'Sponsoring & Partenariats' => 'Sponsoring & Partenariats',
                    'Animation & Production Artistique' => 'Animation & Production Artistique',
                    'Technique & Audiovisuel' => 'Technique & Audiovisuel',
                    'Logistique & Opérations' => 'Logistique & Opérations',
                    'Relationnel & Accueil' => 'Relationnel & Accueil',
                    'Digital & Innovation' => 'Digital & Innovation'
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('salaire', NumberType::class, [
                'label' => 'Salaire (optionnel)',
                'required' => false,
                'scale' => 2,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: 2500.00'
                ],
                'html5' => true,
                'help' => 'Montant en DT'
            ])
            ->add('competences_requises', TextareaType::class, [
                'label' => 'Compétences requises (optionnel)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Listez les compétences et qualifications nécessaires'
                ],
                'help' => '1000 caractères maximum'
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offre::class,
            'attr' => ['class' => 'needs-validation', 'novalidate' => 'novalidate']
        ]);
    }
}