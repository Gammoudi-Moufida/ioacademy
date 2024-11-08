<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\ResetPasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Repository\CategoryRepository;

#[Route('/resetpassword')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;
    private $resetPasswordHelper;
    private $entityManager;

    public function __construct(ResetPasswordHelperInterface $resetPasswordHelper, EntityManagerInterface $entityManager)
    {
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->entityManager = $entityManager;
    }

    #[Route('', name: 'app_forgot_password_request')]
    public function request(Request $request, MailerInterface $mailer): Response
    {
        $email = $request->get('email');
        return $this->processSendingPasswordResetEmail(
            $email,
            $mailer
        );
    }

    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(Request $request, UserPasswordHasherInterface $userPasswordHasher, string $token = null, CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        if ($token) {
            $this->storeTokenInSession($token);
            return $this->redirectToRoute('app_reset_password');
        }
        $token = $this->getTokenFromSession();
        if (null === $token) {
            return $this->redirectToRoute('home');
        }
        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            return $this->redirectToRoute('home');
        }
        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->resetPasswordHelper->removeResetRequest($token);
            $encodedPassword = $userPasswordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            );
            $user->setPassword($encodedPassword);
            $this->entityManager->flush();
            $this->cleanSessionAfterReset();
            $this->addFlash('success', 'Votre mot de passe à bien été changé !');
        }
        $form_register = $this->createForm(RegistrationFormType::class, $user);

        return $this->render('authentification/reset.html.twig', [
            'resetForm' => $form->createView(),
            'registrationForm' => $form_register->createView(),
            'categories' => $categories,

        ]);
    }

    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer): Response
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'mail' => $emailFormData,
        ]);
        if (!$user) {
            return new JsonResponse("noexist");
        }
        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            return new JsonResponse("err_token");
        }

        $email = (new TemplatedEmail())
            ->from(new Address('abdelhafidh.ha@gmail.com', 'Io-Academy'))
            ->to($user->getMail())
            ->subject('Reinitialisation mot de passe')
            ->htmlTemplate('email/reset_password.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ]);
        $mailer->send($email);
        $this->setTokenObjectInSession($resetToken);
        return new JsonResponse("email_sent");
    }
}
