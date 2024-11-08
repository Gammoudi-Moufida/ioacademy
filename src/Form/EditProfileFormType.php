<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class EditProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName',TextType::class)
            ->add('lastName')
            ->add('mail',EmailType::class)
            ->add('numTel')
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
            ->add('country',TextType::class)
            ->add('city',TextType::class)
            ->add('street',TextType::class)
            ->add('adress',TextType::class)
            ->add('zipcode',TextType::class)
            ->add('profession',TextType::class)
            ->add('experience',ChoiceType::class, [
                'choices'  => [ 'Experience'=>'-1',
                                'Stagiaire / apprenti(e)' => 'Stagiaire / apprenti(e)',
                                'Junior / débutant(e) (0 à 2 ans d\'expérience)' => 'Junior / débutant(e) (0 à 2 ans d\'expérience)',
                                'Intermédiaire (plus de 2 ans d\'expérience)' => 'Intermédiaire (plus de 2 ans d\'expérience)',
                                'Dirigeant(e) / responsable' => 'Dirigeant(e) / responsable',
                                'Responsable senior /Directeur(trice)' => 'Responsable senior /Directeur(trice)',
                                'Cadre supérieur' => 'Cadre supérieur',
                                'Sans objet' =>'Sans objet'
                            ],
                'mapped' => true,
                'expanded' => false,
            ])
            ->add('diploma',ChoiceType::class, [
                    'choices'  => ['Diplôme'=>'-1',
                                    'Moins qu\'un diplôme d\'enseignement secondaire (ou équivalent)' => 'Moins qu\'un diplôme d\'enseignement secondaire (ou équivalent)',
                                    'Diplôme d\'enseignement secondaire (ou équivalent)' => 'Diplôme d\'enseignement secondaire (ou équivalent)',
                                    'Études universitaires (sans diplôme)' => 'Études universitaires (sans diplôme)',
                                    'Diplôme sanctionnant 2 années d\'études universitaires' => 'Diplôme sanctionnant 2 années d\'études universitaires',
                                    'Licence' => 'Licence',
                                    'Master' => 'Master',
                                    'Diplôme d\'école professionnelle (p. ex. médecine, chirurgie dentaire, médecine vétérinaire, droit)' =>
                                    'Diplôme d\'école professionnelle (p. ex. médecine, chirurgie dentaire, médecine vétérinaire, droit)',
                                    'Doctorat' => 'Doctorat'
                                
                                ],
                    'mapped' => true,
                    'expanded' => false,
                ])
            ->add('speciality',ChoiceType::class, [
                'choices'  => [ 'Spécialité' => '-1',
                                'Business' => 'Business',
                                'Informatique' => 'Informatique',
                                'Ingénierie' => 'Ingénierie',
                                'Mathématiques et statistiques' => 'Mathématiques et statistiques',
                                'Sciences physiques' => 'Sciences physiques',
                                'Sciences biologiques' => 'Sciences biologiques',
                                'Professions de la santé' => 'Professions de la santé',
                                'Professions juridiques' => 'Professions juridiques',
                                'Éducation' => 'Éducation',
                                'Sciences sociales' => 'Sciences sociales',
                                'Arts et science humaines' => 'Arts et science humaines',
                                'Autre' => 'Autre',
                            
                            ],
                'mapped' => true,
                'expanded' => false,
            ])
            ->add('university',TextType::class)
            ->add('professionalGoal',TextType::class)
            ->add('sectorGoal',TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
