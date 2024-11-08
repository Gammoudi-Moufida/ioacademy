<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueEntity(fields={"mail"}, message="Il existe déjà un compte avec ce mail !")
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Assert\NotBlank(message:"Le champ prénom requis !")]
    #[ORM\Column(type: 'string', length: 255)]
    private $firstName;

    #[Assert\NotBlank(message:"Le champ prénom requis !")]
    #[ORM\Column(type: 'string', length: 255)]
    private $lastName;

    #[Assert\Email(
        message: 'Ce mail {{ value }} n\'est pas valide !',
    )]
    #[Assert\NotBlank(message:"Le champ adresse mail requis !")]
    #[ORM\Column(type: 'string', length: 255)]
    private $mail;

    #[ORM\Column(type: 'bigint', nullable:true)]
    private $numTel;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message:"Mot de passe requis !")]
    #[Assert\Regex(
        pattern: '/\d/',
        match: true,
        message: 'Votre mot de passe doit contenir un chiffre ou moins',)]
    #[Assert\Length( min:6, minMessage:"Le mot de passe doit comporter au moins 6 caractères.")]
    private $password;

    #[ORM\Column(type: 'string', length: 255, nullable:true)]
    private $country;

    #[ORM\Column(type: 'string', length: 255, nullable:true)]
    private $city;

    #[ORM\Column(type: 'string', length: 255, nullable:true)]
    private $street;

    #[ORM\Column(type: 'string', length: 255, nullable:true)]
    private $adress;

    #[ORM\Column(type: 'string', length: 255, nullable:true)]
    private $zipcode;

    #[ORM\Column(type: 'string', length: 255, nullable:true)]
    #[Assert\Length( max:255, maxMessage:"Le champ biographie ne doit pas dépasser 255 caractères.")]
    private $biography;

    #[ORM\Column(type: 'string', length: 255, nullable:true)]
    private $picture;

    #[ORM\Column(type: 'json')]
    private $role = [];

    
    #[ORM\Column(type: 'string', length: 255, nullable:true)]
    private $profession;

    #[ORM\Column(type: 'string', length: 255, nullable:true)]
    private $experience;

    #[ORM\Column(type: 'string', length: 255, nullable:true)]
    private $employ;

    #[ORM\Column(type: 'string', length: 255, nullable:true)]
    private $diploma;
   
    #[ORM\Column(type: 'string', length: 255, nullable:true)]
    private $university;

    #[ORM\Column(type: 'string', length: 255, nullable:true)]
    private $speciality;

    #[ORM\Column(type: 'string', length: 255, nullable:true)]
    private $professionalGoal;

    #[ORM\Column(type: 'string', length: 255, nullable:true)]
    private $sectorGoal;


    #[ORM\Column(type: 'string', length: 50)]
    private $status = 'Active';

    #[ORM\Column(type:"string", length:10)]
    private $isVerified = 1;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: TrackFormation::class)]
    private $trackFormations;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Formation::class)]
    private $formations;

    public function __construct()
    {
        $this->trackFormations = new ArrayCollection();
        $this->formations = new ArrayCollection();
    }

   
    public function getId(): ?int
    {
        return $this->id;
    }
    /** 
      *
      * @see UserInterface
    */
    public function getUserIdentifier(): string
    {
        return (string) $this->mail;
    }
    /**
      *
      * @see UserInterface
    */
    public function getUserName(): ?string
    {
        return $this->firstName;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getNumTel(): ?int
    {
        return $this->numTel;
    }

    public function setNumTel(int $numTel): self
    {
        $this->numTel = $numTel;

        return $this;
    }

    /**
        * @see UserInterface
    */
    public function getPassword(): ?string
    {
            return (string) $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    /**
        * @see UserInterface
    */
    public function getRoles(): ?array
    {
        $roles = $this->role;

        return array_unique($roles);
    }

    public function setRoles(array $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getProfession(): ?string
    {
        return $this->profession;
    }

    public function setProfession(string $profession): self
    {
        $this->profession = $profession;
        return $this;
    }

    public function getExperience(): ?string
    {
        return $this->experience;
    }

    public function setExperience(string $experience): self
    {
        $this->experience = $experience;
        return $this;
    }

    public function getEmploy(): ?string
    {
        return $this->employ;
    }

    public function setEmploy(string $employ): self
    {
        $this->employ = $employ;
        return $this;
    }

    public function getUniversity(): ?string
    {
        return $this->university;
    }

    public function setUniversity(string $university): self
    {
        $this->university = $university;
        return $this;
    }

    public function getSpeciality(): ?string
    {
        return $this->speciality;
    }

    public function setSpeciality(string $speciality): self
    {
        $this->speciality = $speciality;
        return $this;
    }

    public function getProfessionalGoal(): ?string
    {
        return $this->professionalGoal;
    }

    public function setProfessionalGoal(string $professionalGoal): self
    {
        $this->professionalGoal = $professionalGoal;
        return $this;
    }

    
    public function getDiploma(): ?string
    {
        return $this->diploma;
    }

    public function setDiploma(string $diploma): self
    {
        $this->diploma = $diploma;
        return $this;
    }

    public function getSectorGoal(): ?string
    {
        return $this->sectorGoal;
    }

    public function setSectorGoal(string $sectorGoal): self
    {
        $this->sectorGoal = $sectorGoal;
        return $this;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(?string $biography): self
    {
        $this->biography = $biography;
        return $this;
    }
    

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;
        return $this;
    }

    /**
     * @return Collection|TrackFormation[]
     */
    public function getTrackFormations(): Collection
    {
        return $this->trackFormations;
    }

    public function addTrackFormation(TrackFormation $trackFormation): self
    {
        if (!$this->trackFormations->contains($trackFormation)) {
            $this->trackFormations[] = $trackFormation;
            $trackFormation->setUser($this);
        }

        return $this;
    }

    public function removeTrackFormation(TrackFormation $trackFormation): self
    {
        if ($this->trackFormations->removeElement($trackFormation)) {
            // set the owning side to null (unless already changed)
            if ($trackFormation->getUser() === $this) {
                $trackFormation->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Formation[]
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    public function addFormation(Formation $formation): self
    {
        if (!$this->formations->contains($formation)) {
            $this->formations[] = $formation;
            $formation->setUser($this);
        }

        return $this;
    }

    public function removeFormation(Formation $formation): self
    {
        if ($this->formations->removeElement($formation)) {
            // set the owning side to null (unless already changed)
            if ($formation->getUser() === $this) {
                $formation->setUser(null);
            }
        }

        return $this;
    }

    /**
        * @see UserInterface
    */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
       * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
    

    /**
     * Get the value of zipcode
     */ 
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * Set the value of zipcode
     *
     * @return  self
     */ 
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get the value of street
     */ 
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set the value of street
     *
     * @return  self
     */ 
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get the value of city
     */ 
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set the value of city
     *
     * @return  self
     */ 
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get the value of country
     */ 
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set the value of country
     *
     * @return  self
     */ 
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get the value of status
     */ 
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */ 
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of isVerified
     */ 
    public function getIsVerified()
    {
        return $this->isVerified;
    }

    /**
     * Set the value of isVerified
     *
     * @return  self
     */ 
    public function setIsVerified($isVerified)
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
