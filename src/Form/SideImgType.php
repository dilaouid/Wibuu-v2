<?php

namespace App\Form;

use App\Entity\SideImg;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SideImgType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('position', TextType::class, [
                'attr' => ['class' => 'radiusInput', 'value' => 'top / center', 'minlength' => 10]
            ])
            ->add('anime', TextType::class, [
                'label' => 'Nom de l\'anime',
                'attr' => ['class' => 'radiusInput']
            ])
            ->add('link', TextType::class, [
                'attr' => ['class' => 'radiusInput', 'placeholder' => 'https://www.youtube.com/watch?v=z04ajfqA378', 'minlength' => 32]
            ])
            ->add('file', FileType::class, [
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                'required' => true,
                'attr' => ['accept' => "image/*"],
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
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SideImg::class,
        ]);
    }
}
