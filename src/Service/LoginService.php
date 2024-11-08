<?php
namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;

class LoginService
{
    private UserAuthenticatorInterface $userAuthenticator;
    private FormLoginAuthenticator $formLoginAuthenticator;

    /**
     * @param UserAuthenticatorInterface $userAuthenticator
     * @param FormLoginAuthenticator $formLoginAuthenticator
     */
    public function __construct(UserAuthenticatorInterface $userAuthenticator, FormLoginAuthenticator $formLoginAuthenticator)
    {
        $this->userAuthenticator = $userAuthenticator;
        $this->formLoginAuthenticator = $formLoginAuthenticator;
    }


    public function login(User $user, Request $request): void
    {
        $this->userAuthenticator->authenticateUser($user, $this->formLoginAuthenticator, $request);
    }
}