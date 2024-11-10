<?php

namespace App\Form;

use App\Entity\User;
use App\Controller\RegistrationController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    private $allowedDomains = [
        'gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'icloud.com', 'aol.com',
        'protonmail.com', 'zoho.com', 'yandex.com', 'mail.com', 'gmx.com', 'me.com',
        'fastmail.com', 'tutanota.com', 'rocketmail.com', 'msn.com', 'live.com',
        'comcast.net', 'verizon.net', 'btinternet.com', 'orange.fr', 'wanadoo.fr',
        'laposte.net', 'sfr.fr', 'free.fr', 'neuf.fr', 'bluewin.ch', 'libero.it',
        'virginmedia.com', 'sky.com'
    ];

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
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les termes.',
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
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
            ]);
    }

    public function validateEmailDomain($email, ExecutionContextInterface $context): void
    {
        $domain = substr(strrchr($email, "@"), 1); // Extraire le domaine de l'email

        if (!in_array($domain, $this->allowedDomains)) {
            $context->buildViolation('Email domain is not allowed. Please use a valid domain.')
                ->atPath('email')
                ->addViolation();
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
