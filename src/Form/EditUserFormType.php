<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;


class EditUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('mail',EmailType::class)
            ->add('numTel')
            ->add('password2', RepeatedType::class, [
                'mapped' => false,
                'required' => false,
                'type' => PasswordType::class,
                'constraints' => [
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
            ])
            ->add('country')
            ->add('city')
            ->add('street')
            ->add('adress')
            ->add('zipcode')
            ->add('biography',TextareaType::class,array('attr' => array('rows' => '4')))
            ->add('picture', FileType::class, [
                'mapped' => false,
                'attr' => array('accept' => 'image/jpeg,image/jpg'),
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxWidth' => '200',
                        'maxWidthMessage'=>'La largeur de l\'image sélectionnée est trop grande ({{ width }}px). La largeur maximale autorisée est de 200 pixels.',
                        'minWidth' => '100',
                        'minWidthMessage'=>'La largeur de l\'image sélectionnée est trop petite ({{ width }}px). La largeur minimal autorisée est de 100 pixels.',
                        'mimeTypes'=>'image/*',
                        'mimeTypesMessage'=>'Ce fichier n\'est pas une image valide.',
                    ]),
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Étudiant' => 'ROLE_STUDENT',
                    'Enseignant' => 'ROLE_TEACHER',
                    'Controlleur' => 'ROLE_CONTROLLER',
                    'Administrateur' => 'ROLE_ADMIN'
                ],
                'mapped' => false,
                'expanded' => false,
                'multiple' => false,
                'label' => 'Rôles' 
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
