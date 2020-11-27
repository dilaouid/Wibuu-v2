<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => ['class' => 'shadow-sm radiusInput'],
                    'label_attr' =>  ['class' => 'text-secondary'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Merci de saisir un mot de passe.',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Votre nouveau mot de passe doit contenir au maximum {{ limit }} caractères.',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                    'label' => 'Saissisez votre nouveau mot de passe',
                ],
                'second_options' => [
                    'attr' => ['class' => 'shadow-sm radiusInput'],
                    'label' => 'Confirmez le mot de passe',
                    'label_attr' =>  ['class' => 'text-secondary']
                ],
                'invalid_message' => 'Les mots de passes saisis doivent être identiques !',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
