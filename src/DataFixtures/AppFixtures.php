<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;
use App\Entity\Formation;
use App\Entity\User;
use App\Entity\Chapter;
use App\Entity\Blog;
use App\Entity\Category;

class AppFixtures extends Fixture
{
    private $userPasswordHasherInterface;

    public function __construct (UserPasswordHasherInterface $userPasswordHasherInterface) 
    {
        $this->userPasswordHasherInterface = $userPasswordHasherInterface;
    }
    private static $lang=[
        'Francais',
        'Anglais'
    ];
    private static $school=[
        'Primaire',
        'Secondaire',
        'Supérieur',
    ];
    private static $skill=[
        'Débutant',
        'Amateur',
        'Expert'
    ];
    private static $img=[
        '1648734209.jpg',
        '1649316664.png',
        '1649922978.png',
        '1649427384.jpg'
    ];
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        //load categorie
        $icon = "<svg width='16' height='16' fill='#8c98a4' class='bi bi-code-slash me-2 mb-1' viewBox='0 0 16 16'>
        <path d='M10.478 1.647a.5.5 0 1 0-.956-.294l-4 13a.5.5 0 0 0 .956.294l4-13zM4.854 4.146a.5.5 0 0 1 0 .708L1.707 8l3.147 3.146a.5.5 0 0 1-.708.708l-3.5-3.5a.5.5 0 0 1 0-.708l3.5-3.5a.5.5 0 0 1 .708 0zm6.292 0a.5.5 0 0 0 0 .708L14.293 8l-3.147 3.146a.5.5 0 0 0 .708.708l3.5-3.5a.5.5 0 0 0 0-.708l-3.5-3.5a.5.5 0 0 0-.708 0z'></path></svg>";
        $categorieInf = new Category();
        $categorieInf->setName("Informatique")
            ->setActive(true)
            ->setAlias("INFO")
            ->setIcon($icon);

        $manager->persist($categorieInf);
        $manager->flush();

        
        $formateur = new User();
        $formateur->setFirstName($faker->firstName)
            ->setLastName($faker->lastName)
            ->setMail($faker->safeEmail)
            ->setNumTel("0645124565")
            ->setPassword(
                $this->userPasswordHasherInterface->hashPassword(
                    $formateur, "123456"
                )
            )
            ->setCountry($faker->country)
            ->setCity($faker->city)
            ->setStreet($faker->streetName)
            ->setAdress($faker->address)
            ->setZipCode($faker->postcode)
            ->setBiography($faker->text(200))
            ->setPicture("1649934589_user.jpg")
            ->setRoles(['ROLE_TEACHER'])
            ->setSchoolLevel($faker->randomElement(self::$school))
            ->setStatus('Active')
            ->setIsVerified(1);
        $manager->persist($formateur);
        $etudiant = new User();
        $etudiant->setFirstName($faker->firstName)
            ->setLastName($faker->lastName)
            ->setMail($faker->safeEmail)
            ->setNumTel("0645124565")
            ->setPassword(
                $this->userPasswordHasherInterface->hashPassword(
                    $etudiant, "123456"
                )
            )
            ->setCountry($faker->country)
            ->setCity($faker->city)
            ->setStreet($faker->streetName)
            ->setAdress($faker->address)
            ->setZipCode($faker->postcode)
            ->setBiography($faker->sentence($nbWords = 20, $variableNbWords = true))
            ->setPicture("1649934589_user.jpg")
            ->setRoles(['ROLE_STUDENT'])
            ->setSchoolLevel($faker->randomElement(self::$school))
            ->setStatus('Active')
            ->setIsVerified(1);
        $manager->persist($etudiant);

        $admin = new User();
        $admin->setFirstName($faker->firstName)
            ->setLastName($faker->lastName)
            ->setMail($faker->safeEmail)
            ->setNumTel("0645124565")
            ->setPassword(
                $this->userPasswordHasherInterface->hashPassword(
                    $admin, "123456"
                )
            )
            ->setCountry($faker->country)
            ->setCity($faker->city)
            ->setStreet($faker->streetName)
            ->setAdress($faker->address)
            ->setZipCode($faker->postcode)
            ->setBiography($faker->sentence($nbWords = 30, $variableNbWords = true))
            ->setPicture("1649934589_user.jpg")
            ->setRoles(['ROLE_ADMIN'])
            ->setSchoolLevel($faker->randomElement(self::$school))
            ->setStatus('Active')
            ->setIsVerified(1);
        $manager->persist($admin);
        $manager->flush();


     
        for ($x = 0; $x < 200; $x++) {
        $formation = new Formation();
        $formation->setTitle($faker->sentence($nbWords = 3, $variableNbWords = true))
            ->setDescription($faker->sentence($nbWords = 100, $variableNbWords = true))
            ->setImage($faker->randomElement(self::$img))
            ->setDocument("1649316664_word.docx")
            ->setLanguage($faker->randomElement(self::$lang))
            ->setSkills($faker->randomElement(self::$skill))
            ->setPublicationDate($faker->dateTime($max = 'now', $timezone = null))
            ->setUpdateDate($faker->dateTime($max = 'now', $timezone = null))
            ->setActive(3)
            ->setCategory($categorieInf)
            ->setUser($formateur);
        $manager->persist($admin);
        $manager->flush();
       
        $manager->persist($formation);
        $manager->flush();
        for ($i = 0; $i < 4; $i++) {
            $chapter = new Chapter();
            $chapter->setTitle($faker->sentence($nbWords = 4, $variableNbWords = true));
            $chapter->setDescription($faker->sentence($nbWords = 20, $variableNbWords = true));
            $chapter->setActive(true);
            $chapter->setOrderChapter($i+1);
            $chapter->setPublicationDate($faker->dateTime($max = 'now', $timezone = null));
            $chapter->setFormation($formation);
            $manager->persist($chapter);
            $manager->flush();

            for ($j = 0; $j < 6; $j++) {
                $blog = new Blog();
                $blog->setTitle($faker->sentence($nbWords = 4, $variableNbWords = true));
                $blog->setContent($faker->sentence($nbWords = 700, $variableNbWords = true));
                // $blog->setContent($faker->randomHtml(3,2));
                $blog->setActive(true);
                $blog->setOrderBlog($j+1);
                $blog->setPublicationDate($faker->dateTime($max = 'now', $timezone = null));
                $blog->setChapter($chapter);
                $manager->persist($blog);
                $manager->flush();
            }
        }
        $manager->flush();
    }
    }
}
