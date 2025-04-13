<?php

namespace App\Form;

use App\Entity\Utilisateur;
use App\Entity\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Email;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, ['label' => 'Nom'])
            ->add('prenom', null, ['label' => 'Prénom'])
            ->add('email', null, [
                'label' => 'Adresse Email',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir une adresse email']),
                    new Email(['message' => 'Veuillez entrer une adresse email valide.']),
                ],
            ])
            ->add('phone', null, ['label' => 'Numéro de téléphone'])
            ->add('role', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez un rôle',
                'label' => 'Rôle',
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue(['message' => 'Vous devez accepter nos conditions.']),
                ],
                'label' => "J'accepte les conditions d'utilisation"
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'label' => 'Mot de passe',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un mot de passe']),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
