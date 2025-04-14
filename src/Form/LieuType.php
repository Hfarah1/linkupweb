<?php

namespace App\Form;

use App\Entity\TypeLieu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeLieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomLieu', TextType::class, [
                'label' => 'Nom du lieu',
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
            ])
            ->add('villeLieu', TextType::class, [
                'label' => 'Ville',
            ])
            ->add('typeLieu', TextType::class, [
                'label' => 'Type du lieu',
            ])
            ->add('disponibilite', CheckboxType::class, [
                'label' => 'Disponible ?',
                'required' => false,
            ])
            ->add('prixLocation', NumberType::class, [
                'label' => 'Prix de location (€)',
                'scale' => 2,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TypeLieu::class,
        ]);
    }
}
