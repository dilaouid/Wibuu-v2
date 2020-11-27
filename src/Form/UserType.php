<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => ['class' => 'shadow-sm radiusInput'],
                'label' => 'Nom d\'utilisateur',
                'label_attr' => ['class' => 'text-secondary']
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passes doivent être identiques.',
                'options' => ['attr' => ['class' => 'shadow-sm radiusInput']],
                'first_options'  => ['label' => 'Mot de passe', 'label_attr' =>  ['class' => 'text-secondary']],
                'second_options' => ['label' => 'Confirmez le mot de passe', 'label_attr' =>  ['class' => 'text-secondary']],
                'label_attr' => ['class' => ['text-secondary']],
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'attr' => ['class' => 'shadow-sm radiusInput'],
                'label_attr' => ['class' => 'text-secondary']
            ])
            ->add('firstname')
            ->add('lastname')
            ->add('birthday', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'label_attr' => ['class' => 'text-secondary'],
                'attr' => ['class' => 'shadow-sm radiusInput'],
            ])
            ->add('followers')
            ->add('following')
            ->add('publications')
            ->add('private')
            ->add('banned')
            ->add('cgu', CheckboxType::class, [
                'label' => "En m'inscrivant, je déclare avoir plus de 13 ans et d'avoir lu et accepté les conditions générales d'utilisations",
                'attr' => ['class' => 'custom-control-input'],
                'label_attr' => ['class' => 'custom-control-label']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['Default', 'register', 'forgot_password']
        ]);
    }
}