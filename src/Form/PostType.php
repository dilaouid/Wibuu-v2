<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('posX', RangeType::class, [
                'mapped' => false,
                'attr' => ['class' => 'custom-range', "step" => 2, "value" => 0, "min" => -100, "max" => 500, "id" => "x_pos"],
                'label' => 'Position X'
            ])
            ->add('posY', RangeType::class, [
                'mapped' => false,
                'attr' => ['class' => 'custom-range', "step" => 2, "value" => 0, "min" => -100, "max" => 500, "id" => "y_pos"],
                'label' => 'Position Y'
            ])
            ->add('size', RangeType::class, [
                'mapped' => false,
                'attr' => ['class' => 'custom-range', "step" => 2, "value" => 300, "min" => 100, "max" => 540, "id" => "size"],
                'label' => 'Taille'
            ])
            ->add('filterH', NumberType::class, [
                'mapped' => false,
                'required' => true
            ])
            ->add('base64', TextType::class, [
                'mapped' => false,
                'attr' => ['style' => 'display:none'],
                'label_attr' => ['style' => 'display:none;']
            ])
            ->add('file', FileType::class, [
                'mapped' => false,
                'required' => true,
                'attr' => ['style' => 'display:none'],
                'label_attr' => ['style' => 'display:none'],
                'row_attr' => ['style' => 'display:none']
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['style' => 'font-weight: bold;']
            ])
            ->add('position', TextType::class, [
                'attr' => ['class' => 'radiusInput', 'placeholder' => "Où avez-vous prit cette photo ?"],
                'label_attr' => ['style' => 'font-weight: bold;'],
                'required' => false
            ])
            ->add('identificationPosts', TextType::class, [
                'mapped' => false,
                'attr' => ['class' => 'radiusInput', 'placeholder' => "Noms d'utilisateurs séparés par un espace"],
                'label_attr' => ['style' => 'font-weight: bold;'],
                'required' => false
            ])
            ->add('private', CheckboxType::class, [
                'label' => 'Privé',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
