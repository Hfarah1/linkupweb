<?php

namespace App\Form;

use App\Entity\Candidature;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;


class CandidatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('cv_upload', FileType::class, [
            'label' => 'CV (PDF file)',
            'mapped' => false, // This field is not mapped directly to the entity
            'required' => true,
            'constraints' => [
                new File([
                    'maxSize' => '2M',
                    'mimeTypes' => ['application/pdf'],
                    'mimeTypesMessage' => 'Please upload a valid PDF document',
                ])
            ],
        ])
        ->add('lettre_motivation', TextareaType::class, [
            'label' => 'Lettre de motivation',
            'required' => true,
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
