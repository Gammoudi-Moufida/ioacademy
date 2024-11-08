<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompleteRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
            ->add('profession',TextType::class)
            ->add('employ',TextType::class)
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
            'csrf_protection' => false,
        ]);
    }
}
