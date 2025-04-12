<?php

namespace App\Form;

use App\Entity\Candidature;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints as Assert;


class CandidatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cv_upload', FileType::class, [
                'label' => 'CV (PDF)',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new Assert\NotNull([
                        'message' => 'Le fichier CV est obligatoire'
                    ]),
                    new Assert\File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['application/pdf'],
                        'mimeTypesMessage' => 'Seuls les fichiers PDF sont acceptés',
                        'disallowEmptyMessage' => 'Le fichier ne peut pas être vide'
                    ])
                ],
                'attr' => [
                    'accept' => '.pdf'
                ]
            ])
            ->add('lettre_motivation', TextareaType::class, [
                'label' => 'Lettre de motivation',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 8,
                    'placeholder' => 'Expliquez pourquoi vous êtes le candidat idéal pour ce poste...',
                    'data-minlength' => 20,
                    'data-maxlength' => 2000
                ],
                'help' => '20 à 2000 caractères (minimum 20, maximum 2000)'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidature::class,
        ]);
    }
}
