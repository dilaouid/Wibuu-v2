<?php

namespace App\Form;

use App\Entity\Filters;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class FiltersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,
            ['label' => 'Nom du filtre',
            'attr' => ['class' => 'shadow-sm radiusInput'],
            'label_attr' => ['class' => 'text-secondary']
            ])
            ->add('origin', TextType::class,
                ['label' => 'Origine du filtre',
                'attr' => ['class' => 'shadow-sm radiusInput'],
                'label_attr' => ['class' => 'text-secondary']
            ])
            ->add('tags', TextType::class,
                ['label' => 'Tags (séparés par des virgules)',
                'attr' => ['class' => 'shadow-sm radiusInput', 'placeholder' => 'Fate, FGO, Fate Grand Order, ...'],
                'label_attr' => ['class' => 'text-secondary']
            ])
            ->add('file', FileType::class, [
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                'required' => true,
                'attr' => ['accept' => "image/*", 'id' => 'form_file'],
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => 'image/*',
                        'mimeTypesMessage' => 'Merci d\'uploader un fichier valide',
                    ])
                ],
            ])
        ;

        $builder->get('tags')
            ->addModelTransformer(new CallbackTransformer(
                function ($tagsAsArray) {
                    // transform the array to a string
                    return implode(', ', $tagsAsArray);
                },
                function ($tagsAsString) {
                    // transform the string back to an array
                    return explode(', ', $tagsAsString);
                }
            ))
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Filters::class
        ]);
    }
}