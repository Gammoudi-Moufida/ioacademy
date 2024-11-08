<?php

namespace App\Form;

use App\Entity\Formation;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotNull;

class FormationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
            ->add('title',TextType::class)
            ->add('description',TextareaType::class,array('attr' => array('rows' => '3')))
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => function(Category $category) {
                    return $category->getName();
                },
                'placeholder' => 'Choisir une categorie'
            ])
            ->add('image', FileType::class, [
                'mapped' => false,
                'attr' => array('accept' => 'image/jpeg,image/jpg,image/png'),
                'required' => false,
                'constraints' => [
                    new Image([
                        'mimeTypes'=>'image/*',
                        'mimeTypesMessage'=>'Ce fichier n\'est pas une image valide.',
                        'allowPortrait' => false,
                        'allowPortraitMessage'=>'Les images orientées portrait ne sont pas autorisées',
                        'allowSquare'=>false,
                        'allowSquareMessage'=>'Les images carrées ne sont pas autorisées'
                    ]),
                ],
            ])
            ->add('document', FileType::class, [
                'mapped' => false,
                'attr' => array('accept' => '.docx'),
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'mimeTypesMessage'=>'Ce fichier n\'est pas une document word valide !',
                    ]),
                    new NotNull([
                        'message' => 'Veuillez sélectionner un document !',
                    ]),
                    
                ],
            ])
            ->add('language', ChoiceType::class, [
                'choices'  => [
                    'Francais' => 'Francais',
                    'Anglais' => 'Anglais',
                ],
                'multiple'=>false,
                'expanded'=>true,
            ])
            ->add('skills', ChoiceType::class, [
                'choices'  => [
                    'Débutant' => 'Débutant',
                    'Amateur' => 'Amateur',
                    'Expert' => 'Expert',
                ],
                'multiple'=>false,
                'expanded'=>true,
                'label_attr'=>[
                    'class'=>'form-check-inline'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
