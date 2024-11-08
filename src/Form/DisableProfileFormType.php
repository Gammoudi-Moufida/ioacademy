<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DisableProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('disableaccount', CheckboxType::class, [
                'mapped' => false,
                'required' => 'required',
                'constraints' => [
                    new IsTrue([
                        'message' => 'Êtes-vous sûr de vouloir désactiver votre compte'
                    ])
                ],
                'label'    => 'Je confirme que je veux désactiver mon compte.',
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
