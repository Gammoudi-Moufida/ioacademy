<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;


class ResetPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new NotBlank(['message' => 'Mot de passe requis !',]),
                    new Length(['min' => 6 , 'minMessage' => 'Le mot de passe doit comporter au moins 6 caractères.']),
                    new Regex(array(
                        'pattern'   => '/\d/',
                        'match'     => true,
                        'message'   => 'Votre mot de passe doit contenir un chiffre ou moins.'
                    )),   
                ],
                'invalid_message' => 'Les champs du mot de passe doivent correspondre !',
                'options' => ['attr' => ['class' => 'password-field']],
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        
    }
}
