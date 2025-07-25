<?php

namespace App\Form;

use App\Entity\Offre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\DataTransformerInterface;

class OffreType extends AbstractType
{
    private static $regionsAndCities = [
        'Tunis' => [null, 'Tunis', 'La Marsa', 'Carthage', 'Le Bardo', 'Lac 1', 'Lac 2'],
        'Ariana' => [null, 'Ariana', 'Raoued', 'Soukra', 'Kalaat El Andalous'],
        'Ben Arous' => [null, 'Ben Arous', 'Rades', 'Hammam Lif', 'Hammam Chatt'],
        'Manouba' => [null, 'Manouba', 'Douar Hicher', 'Oued Ellil', 'Tebourba'],
        'Nabeul' => [null, 'Hammamet', 'Nabeul', 'Korba', 'Kelibia', 'Beni Khiar'],
        'Bizerte' => [null, 'Bizerte', 'Menzel Bourguiba', 'Mateur', 'Ras Jebel'],
        'Beja' => [null, 'Beja', 'Testour', 'Teboursouk', 'Nefza'],
        'Jendouba' => [null, 'Jendouba', 'Tabarka', 'Ain Draham', 'Fernana'],
        'Kef' => [null, 'Kef', 'Dahmani', 'Tajerouine', 'Nebeur'],
        'Siliana' => [null, 'Siliana', 'Makthar', 'Bourouis', 'Gaafour'],
        'Zaghouan' => [null, 'Zaghouan', 'El Fahs', 'Nadhour', 'Bir Mcherga'],
        'Sousse' => [null, 'Sousse', 'Hammam Sousse', 'Sahloul', 'Kalaa Kebira'],
        'Monastir' => [null, 'Monastir', 'Sahline', 'Ksar Hellal', 'Jemmal'],
        'Mahdia' => [null, 'Mahdia', 'Chebba', 'Melloulech', 'Ksour Essef'],
        'Sfax' => [null, 'Sfax Ville', 'Sakiet Ezzit', 'Sakiet Eddaier', 'Thyna'],
        'Kairouan' => [null, 'Kairouan', 'Haffouz', 'Oueslatia', 'Sbikha'],
        'Kasserine' => [null, 'Kasserine', 'Sbeitla', 'Feriana', 'Thala'],
        'Sidi Bouzid' => [null, 'Sidi Bouzid', 'Regueb', 'Meknassy', 'Jilma'],
        'Gabes' => [null, 'Gabes', 'Chenini Gabes', 'Mareth', 'Metouia'],
        'Medenine' => [null, 'Medenine', 'Djerba', 'Zarzis', 'Ben Guerdane'],
        'Tataouine' => [null, 'Tataouine', 'Bir Lahmar', 'Remada', 'Ghomrassen'],
        'Gafsa' => [null, 'Gafsa', 'Metlaoui', 'Redeyef', 'Mdhilla'],
        'Tozeur' => [null, 'Tozeur', 'Nefta', 'Degache', 'Tameghza'],
        'Kebili' => [null, 'Kebili', 'Douz', 'Souk Lahad', 'Faouar'],
    ];



    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $regions = array_keys(self::$regionsAndCities);
        $selectedGouvernorat = $options['selected_gouvernorat'] ?? null;

        // Déterminer les villes initiales en fonction du gouvernorat sélectionné
        $initialCities = [];
        if ($selectedGouvernorat && isset(self::$regionsAndCities[$selectedGouvernorat])) {
            $initialCities = array_filter(self::$regionsAndCities[$selectedGouvernorat], fn($city) => $city !== null);
        }

        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre de l\'offre*',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Développeur Symfony Senior'
                ],
                'help' => '100 caractères maximum'
            ])
            ->add('gouvernorat', ChoiceType::class, [
                'label' => 'Gouvernorat*',
                'choices' => array_combine($regions, $regions),
                'placeholder' => 'Sélectionnez un gouvernorat',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'gouvernorat_select'
                ],
                'data' => $selectedGouvernorat
            ])
            ->add('ville', ChoiceType::class, [
                'label' => 'Ville*',
                'choices' => [],
                'placeholder' => 'Sélectionnez une ville',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'ville_select'
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
                'mapped' => false,
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
                'placeholder' => 'Sélectionnez un type de contrat',
                'required' => true,
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
                'placeholder' => 'Sélectionnez une catégorie',
                'required' => true,
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
            ]);

        // PRE_SUBMIT event to dynamically set ville choices
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            $gouvernorat = $data['gouvernorat'] ?? null;

            $cities = $gouvernorat && isset(self::$regionsAndCities[$gouvernorat])
                ? array_filter(self::$regionsAndCities[$gouvernorat], fn($city) => $city !== null)
                : [];

            $form->add('ville', ChoiceType::class, [
                'label' => 'Ville*',
                'choices' => array_combine($cities, $cities),
                'placeholder' => 'Sélectionnez une ville',
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'ville_select'
                ]
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offre::class,
            'attr' => ['class' => 'needs-validation', 'novalidate' => 'novalidate'],
            'selected_gouvernorat' => null,
        ]);
    }

    public static function getRegionsAndCities(): array
    {
        return self::$regionsAndCities;
    }
}
