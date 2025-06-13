<?php

namespace App\Form;

use App\Entity\Orders;
use App\Entity\Products;
use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class ArticleForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('articles', CollectionType::class, [
                'entry_type' => ArticleForm::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Articles',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter les articles',
                'attr' => ['class' => 'btn btn-primary']
            ])
            ->add('category', null, [
                'label' => 'Catégorie de l\'article',
               'attr' => ['placeholder' => '-- Sélectionnez une catégorie --'],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner une catégorie'
                    ])
                ]
            ])
            ->add('quantity', IntegerType::class, [
                'constraints' => [
                    new PositiveOrZero([
                        'message' => 'La quantité doit être positive ou nulle'
                    ])
                ]
            ])
            ->add('created_at', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => true
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'article',
                'attr' => ['placeholder' => 'Entrez ici le titre de l\'article'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le titre est obligatoire'
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Le titre ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('number', IntegerType::class, [
                'constraints' => [
                    new Positive([
                        'message' => 'Le numéro doit être positif'
                    ])
                ]
            ])
            ->add('rarity', ChoiceType::class, [
                'choices' => [
                    'Commune' => 'common',
                    'Peu commune' => 'uncommon',
                    'Rare' => 'rare',
                    'Très rare' => 'very_rare',
                    'Légendaire' => 'legendary'
                ]
            ])
            ->add('description', TextType::class, [
                'label' => 'Description de l\'article',

                'attr' => ['placeholder' => 'Entrez ici la description de l\'article'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'La description est obligatoire'
                    ]),
                    new Length([
                        'max' => 1000,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Image de l\'article',
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez télécharger une image'
                    ]),
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif'
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG ou GIF)'
                    ])
                ]
            ])
            ->add('video', FileType::class, [
                'label' => 'Vidéo de l\'article (optionnel)',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '50M',
                        'mimeTypes' => [
                            'video/mp4',
                            'video/quicktime'
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une vidéo valide (MP4 ou MOV)'
                    ])
                ]
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix de l\'article',
                'currency' => 'EUR',
                'constraints' => [
                    new Positive([
                        'message' => 'Le prix doit être positif'
                    ])
                ]
            ])
            ->add('state', ChoiceType::class, [
                'label' => 'État de l\'article',
                'choices' => [
                    'Neuf' => 'new',
                    'Très bon état' => 'very_good',
                    'Bon état' => 'good',
                    'Satisfaisant' => 'satisfactory'
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de l\'article',
                'choices' => [
                    'Carte' => 'card',
                    'Figurine' => 'figurine',
                    'Accessoire' => 'accessory',
                    'Autre' => 'other'
                ]
            ])
            ->add('extension', ChoiceType::class, [
                'label' => 'Extension de l\'article',
                'choices' => [
                    'Base Set' => 'base_set',
                    'Jungle' => 'jungle',
                    'Fossil' => 'fossil',
                    'Team Rocket' => 'team_rocket'
                ]
            ])
            ->add('orders', EntityType::class, [
                'class' => Orders::class,
                'choice_label' => 'id',
                'multiple' => true,
                'required' => false
            ])
            ->add('users', EntityType::class, [
                'class' => Users::class,
                'choice_label' => 'id',
                'multiple' => true,
                'required' => false
            ])
            ->add('create_product_users', EntityType::class, [
                'class' => Users::class,
                'choice_label' => 'id',
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
