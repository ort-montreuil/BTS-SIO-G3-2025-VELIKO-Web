<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class ResetMdpFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('email', EmailType::class,
                [
                    'label' => "ADRESSE E-MAIL : "
                ])
            ->add('newPassword', RepeatedType::class, [
                'label'=>"NOUVEAU MOT DE PASSE : ",
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Nouveau mot de passe',
                    ],
                    'label' => 'Nouveau password',
                    'label_attr' => [
                        'class' => 'form-label mt-4',
                    ],
                ],
                'second_options' => [
                    'attr' => [
                        'label'=>"CONFIRMATION DU MOT DE PASSE : ",
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
}