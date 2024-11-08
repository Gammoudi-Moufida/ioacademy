<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName',TextType::class)
            ->add('lastName')
            ->add('mail',EmailType::class)
            ->add('password', PasswordType::class);
            
            // ->add('schoolLevel', ChoiceType::class, [
            //     'choices'  => ['Primaire' => 'Primaire', 'Secondaire' => 'Secondaire', 'Supérieur' => 'Supérieur'],
            //     'mapped' => true,
            //     'expanded' => false,
            //     'label' => 'Niveau d\'etude',
            // ])
            // ->add('role', ChoiceType::class, [
            //       'choices'  => ['Étudiant' => 'Student', 'Enseignant' => 'Teacher'],
            //       'mapped' => false,
            //       'expanded' => false,
            //       'label' => 'Role',
            // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
