<?php
namespace App\Form;

use App\Entity\Materiel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class MaterielType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomMat', TextType::class, ['label' => 'Nom du matériel'])
            ->add('descriptionMat', TextType::class, ['label' => 'Description'])
            ->add('categorieMat', TextType::class, ['label' => 'Catégorie'])
            ->add('marqueMat', TextType::class, ['label' => 'Marque'])
            ->add('referenceMat', TextType::class, ['label' => 'Référence'])
            ->add('quantiteStock', IntegerType::class, ['label' => 'Quantité en stock'])
            ->add('quantiteReservee', IntegerType::class, ['label' => 'Quantité réservée'])
            ->add('idEv', IntegerType::class, ['label' => 'ID Evénement', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Materiel::class,
        ]);
    }
} 