<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer une adresse email',
                    ]),
                    new Callback([$this, 'validateEmailDomain']),
                ],
            ])
            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer votre nom',
                    ]),
                ],
            ])
            ->add('prenom', TextType::class, [
                new NotBlank([
                    'message' => 'Entrer votre prenom',
                ]),
            ])
            ->add('date_naissance', DateType::class, [   new NotBlank([
                'message' => 'Entrer une date de naissance',
            ]),
            ])
            ->add('adresse', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer une adresse',
                    ]),
                    new Regex([
                        'pattern' => '/^\d+\s+.+$/',
                        'message' => 'Entrer une adresse valide commencant par un numero',
                    ]),
                ],
            ])
            ->add('ville', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer une ville',
                    ]),
                ],
            ])

            ->add('codePostal', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer un code postal',
                    ]),
                    new Length([
                        'min' => 5,
                        'max' => 5,
                        'minMessage' => 'Le code postal doit contenir 5 chiffres',
                        'maxMessage' => 'Le code postal doit contenir 5 chiffres',
                    ]),
                ],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
