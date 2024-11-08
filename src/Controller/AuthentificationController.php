<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\ChangeAdresseFormType;
use App\Form\ChangePasswordFormType;
use App\Form\DisableProfileFormType;
use App\Form\EditProfileFormType;
use App\Form\RegistrationFormType;
use App\Form\CompleteRegistrationFormType;
use App\Model\ChangePassword;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Mailer\MailerInterface;
use App\Repository\CategoryRepository;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;

class AuthentificationController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function index(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            if ($error->getMessage() == "InverifiedAccount") {
                return new Response("InverifiedAccount");
            } 
            else if($error->getMessage() == "BlockedAccount")
            {
                return new Response("BlockedAccount");
            }
            else {
                return new Response("Error");
            }
        } else {
            return new JsonResponse("Connected");
        }
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,
     UserCheckerInterface $checker,UserAuthenticatorInterface $userAuthenticator, FormLoginAuthenticator $formLoginAuthenticator)
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()  ) {
            $roles[] = 'ROLE_STUDENT';
            $user->setRoles($roles);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();
            $checker->checkPreAuth($user);
            $userAuthenticator->authenticateUser($user, $formLoginAuthenticator, $request);
            return new JsonResponse('Inserted');
        } 
        else {
            $errors = $this->getErrorsFromForm($form);
            return new JsonResponse($errors);
        }
    }
    #[Route('/complete/registration', name: 'complete-registration')]
    public function completeRegister(Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $form = $this->createForm(CompleteRegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isValid() && $form->isSubmitted() ) {
            if($form->get('diploma')->getData()){
                $user->setDiploma($form->get('diploma')->getData());
            }
            if($form->get('university')->getData()){
                $user->setUniversity($form->get('university')->getData());
            }
            if($form->get('speciality')->getData()){
                $user->setSpeciality($form->get('speciality')->getData());
            }
            if($form->get('profession')->getData()){
                $user->setProfession($form->get('profession')->getData());
            }
            if($form->get('experience')->getData()){
                $user->setExperience($form->get('experience')->getData());
            }
            if($form->get('employ')->getData()){
                $user->setEmploy($form->get('employ')->getData());
            }
            if($form->get('professionalGoal')->getData()){
                $user->setProfessionalGoal($form->get('professionalGoal')->getData());
            }
            if($form->get('sectorGoal')->getData()){
                $user->setSectorGoal($form->get('sectorGoal')->getData());
            }
            $entityManager->persist($user);
            $entityManager->flush();
            return new JsonResponse('Inserted');
        } 
        else {
            $errors = $this->getErrorsFromForm($form);
            return new JsonResponse($errors);
        }
    }

    #[Route('/revalidatemail/{mailaddress}', name: 'revalidatemail')]
    public function resendMail(String $mailaddress, UserRepository $userRepository, VerifyEmailHelperInterface $verifyEmailHelper, MailerInterface $mailer)
    {
        $user = $userRepository->findUserByEmail($mailaddress);
        if(!$user==null){
            if(!$user->getIsVerified())
            {
                $signatureComponents = $verifyEmailHelper->generateSignature(
                    'app_verify_email',
                    $user->getId(),
                    $user->getmail(),
                    ['id' => $user->getId()]
                );
                $mail = (new TemplatedEmail())
                    ->from('abdelhafidh.ha@gmail.com')
                    ->to($user->getMail())
                    ->subject('Validation d\'inscription')
                    ->htmlTemplate('email/inscription.html.twig')
                    ->context([
                        'signedurl' => $signatureComponents->getSignedUrl(),
                        'name' => $user->getlastName() . ' ' . $user->getfirstName(),
                        'date' => date("Y-m-d h:m:s"),
                    ]);
                try {
                    $mailer->send($mail);
                } catch (TransportExceptionInterface $e) {
                    return new JsonResponse('Error');
                }
                return new JsonResponse('EmailSent');
            }
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Not found email');
        }
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Not found email');
        
    }

    #[Route('/verify', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, VerifyEmailHelperInterface $verifyEmailHelper, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->find($request->query->get('id'));
        if (!$user) {
            throw $this->createNotFoundException();
        }
        try {
            $verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getMail(),
            );
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('error', $e->getReason());
            return $this->redirectToRoute('home');
        }
        $user->setIsVerified(true);
        $entityManager->flush();
        return $this->redirectToRoute('home');
    }

    #[Route('/user/profile/address', name: 'user_change_address')]
    public function userProfileAddress(Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $categories = $categoryRepository->findAll();
        $user = $this->getUser();
        $form1 = $this->createForm(ChangeAdresseFormType::class, $user);
        $form1->handleRequest($request);
        if ($form1->isSubmitted() && $form1->isValid()) {
            $user = $form1->getData();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success-adress', 'Votre adresse à bien été changé !');
        }
        return $this->render('user/changeaddress.html.twig', [
            'editaddressForm' => $form1->createView(),
            'categories' => $categories,
            'current' => 'adress'
        ]);
    }

    #[Route('/user/profile', name: 'user_profile')]
    public function userProfile(Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $categories = $categoryRepository->findAll();
        $user = $this->getUser();
        $form = $this->createForm(EditProfileFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $uploadedFile = $form['picture']->getData();
            if ($uploadedFile) {
                $fs = new Filesystem();
                $fs->remove($this->getParameter('kernel.project_dir').'/public/images/userprofile/'.$user->getPicture());
                $destination = $this->getParameter('kernel.project_dir') . '/public/images/userprofile';
                $newFilename = $user->getId() . '.' . $uploadedFile->guessExtension();
                $uploadedFile->move(
                    $destination,
                    $newFilename
                );
                $user->setPicture($newFilename);
            }
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success-profil', 'Vos informations ont été bien modifiées.');
        }
        return $this->render('user/profile.html.twig', [
            'editprofileForm' => $form->createView(),
            'categories' => $categories,
            'current' => 'profile'
        ]);
    }


    #[Route('/user/change/password', name: 'user_change_password')]
    public function changepassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, CategoryRepository $categoryRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $categories = $categoryRepository->findAll();
        $user = $this->getUser();
        $changePassword = new ChangePassword();
        $form = $this->createForm(ChangePasswordFormType::class, $changePassword);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Votre mot de passe à bien été changé !');
            return $this->render('user/changepassword.html.twig', [
                'password_form' => $form->createView(),
                'categories' => $categories,
                'current' => 'password'
            ]);
        }
        return $this->render('user/changepassword.html.twig', [
            'password_form' => $form->createView(),
            'categories' => $categories,
            'current' => 'password'
        ]);
    }

    #[Route('/user/disable/account', name: 'user_disable_account')]
    public function disableaccount(Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $categories = $categoryRepository->findAll();
        $user = $this->getUser();
        $form = $this->createForm(DisableProfileFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setStatus('inactive');
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('logout'));
        }
        return $this->render('user/disableprofile.html.twig', [
            'disableprofileForm' => $form->createView(),
            'categories' => $categories,
            'current' => 'disable'
        ]);
    }


    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }
        return $errors;
    }
}
