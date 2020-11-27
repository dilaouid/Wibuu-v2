<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => ['class' => 'shadow-sm radiusInput'],
                'label' => 'Nom d\'utilisateur',
                'label_attr' => ['class' => 'text-secondary']
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => "En m'inscrivant, je déclare avoir plus de 13 ans et d'avoir lu et accepté les conditions générales d'utilisations",
                'attr' => ['class' => 'custom-control-input'],
                'label_attr' => ['class' => 'custom-control-label'],
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez acceptez nos termes et conditions',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'attr' => ['class' => 'shadow-sm radiusInput'],
                'label_attr' => ['class' => 'text-secondary']
            ])
            ->add('birthday', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'label_attr' => ['class' => 'text-secondary'],
                'attr' => ['class' => 'shadow-sm radiusInput'],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passes doivent être identiques.',
                'options' => ['attr' => ['class' => 'shadow-sm radiusInput']],
                'first_options'  => ['label' => 'Mot de passe', 'label_attr' =>  ['class' => 'text-secondary']],
                'second_options' => ['label' => 'Confirmez le mot de passe', 'label_attr' =>  ['class' => 'text-secondary']],
                'label_attr' => ['class' => ['text-secondary']],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
