<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
 
class ChangePassword
{
    /**
     * @SecurityAssert\UserPassword(
     *     message = "Ce mot de passe est incorrect !"
     * )
     */
    protected $oldPassword;
     
    #[Assert\NotBlank(message:"Mot de passe requis !")]
    #[Assert\Regex(
        pattern: '/\d/',
        match: true,
        message: 'Votre mot de passe doit contenir un chiffre ou moins',)]
    #[Assert\Length( min:6, minMessage:"Le mot de passe doit comporter au moins 6 caractères.")]
    protected $password;
             
     
    function getOldPassword() {
        return $this->oldPassword;
    }
 
    function getPassword() {
        return $this->password;
    }
 
    function setOldPassword($oldPassword) {
        $this->oldPassword = $oldPassword;
        return $this;
    }
 
    function setPassword($password) {
        $this->password = $password;
        return $this;
    }
}