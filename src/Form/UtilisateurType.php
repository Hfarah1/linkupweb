<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class,[
                'label'=>'Enter name',
                'attr'=>[
                    'placeholder' => 'ex:foulani'
                ]
                
            ])
            ->add('prenom',TextType::class,[
                'label'=>'Enter lastname',
                'attr'=>[
                    'placeholder' => 'ex:foulan'
                ]
                
            ])
            ->add('email',TextType::class,[
                'label'=>'Enter email',
                'attr'=>[
                    'placeholder' => 'ex:foulani.foulan@gmail.com'
                ]
                
            ])
            ->add('pwd',TextType::class,[
                'label'=>'Enter password',
                'attr'=>[
                    'placeholder' => 'ex:dsfoe3q2ekf'
                ]
                
            ])
            ->add('phone',TextType::class,[
                'label'=>'Enter phone number',
                'attr'=>[
                    'placeholder' => 'ex:98 344 867'
                ]
                
            ])
            ->add('role', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'name',
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
