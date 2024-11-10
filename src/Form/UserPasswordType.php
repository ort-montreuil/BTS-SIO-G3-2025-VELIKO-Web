<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', PasswordType::class, [ // Ajout du champ pour le mot de passe actuel
                'mapped'=> false,
                'attr' => [
                    'autocomplete' => 'current-password',
                    'placeholder' => 'Ancien mot de passe',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre mot de passe actuel.',
                    ]),
                ],
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Nouveau mot de passe',
                    ],
                    'label' => 'NOUVEAU MOT DE PASSE',
                    'label_attr' => [
                        'class' => 'form-label mt-4',
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Entrer un mot de passe',
                        ]),
                        new Regex([
                            'pattern' => '/[A-Z]/',
                            'message' => 'Votre mot de passe doit contenir au moins une lettre majuscule',
                        ]),
                        new Regex([
                            'pattern' => '/[a-z]/',
                            'message' => 'Votre mot de passe doit contenir au moins une lettre minuscule',
                        ]),
                        new Regex([
                            'pattern' => '/\d/',
                            'message' => 'Votre mot de passe doit contenir au moins un chiffre',
                        ]),
                        new Regex([
                            'pattern' => '/[\W]/',
                            'message' => 'Votre mot de passe doit contenir au moins un caractère spécial',
                        ]),
                        new Length([
                            'min' => 12,
                            'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                            'max' => 4096,
                        ]),
                    ],
                ],
                'second_options' => [
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Confirmez le mot de passe',
                    ],
                    'label' => 'Confirmation du password',
                    'label_attr' => [
                        'class' => 'form-label mt-4',
                    ],
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null, // Modifier pour ne pas mapper à l'entité User
        ]);
    }
}
