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
            ])
            ->add('nom', TextType::class, [

            ])
            ->add('prenom', TextType::class, [

            ])
            ->add('date_naissance', DateType::class, [
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'years' => range(date('Y'),1900),
                'label'=> 'Date de naissance',
            ])
            ->add('adresse', TextType::class, [
            ])
            ->add('ville', TextType::class, [
            ])
            ->add('codePostal', TextType::class, [
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
