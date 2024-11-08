<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

const EASY = 1;
const MEDIUM = 2;
const HARD = 3;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    public static array $levels = [
        EASY => 'Débutant',
        MEDIUM => 'Amateur',
        HARD => 'Expert',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Assert\NotBlank(message:"Le champ titre requis !")]
    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[Assert\NotBlank(message:"Le champ description requis !")]
    #[ORM\Column(type: 'text')]
    private $description;

    #[ORM\Column(type: 'string',nullable:true, length: 255)]
    private $image;

    #[ORM\Column(type: 'string', nullable:true, length: 255)]
    private $document;

    #[Assert\NotBlank(message:"Selectionner la langue !")]
    #[ORM\Column(type: 'string', length: 255)]
    private $language;

    #[Assert\NotBlank(message:"Selectionner le niveau de difficulté !")]
    #[ORM\Column(type: 'string', length: 255)]
    private $skills;
    
    #[ORM\Column(type: 'date')]
    private $publicationDate;

    #[ORM\Column(type: 'date')]
    private $updateDate;

    #[ORM\Column(type: 'integer')]
    private $active=0;

    #[ORM\OneToMany(mappedBy: 'formation', targetEntity: Chapter::class)]
    #[ORM\OrderBy(["id" => "ASC"])]
    private $chapters;

    #[Assert\NotBlank(message:"Selectionner une categorie !")]
    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'formations')]
    #[ORM\JoinColumn(nullable: false)]
    private $category;

    #[ORM\OneToMany(mappedBy: 'formation', targetEntity: TrackFormation::class)]
    private $trackformations;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'formations')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    public function __construct()
    {
        $this->chapters = new ArrayCollection();
        $this->trackformations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getActive(): ?int
    {
        return $this->active;
    }

    public function setActive(int $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(\DateTimeInterface $publicationDate): self
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }
    

    /**
     * @return Collection|Chapter[]
     */
    public function getChapters(): Collection
    {
        return $this->chapters;
    }

    public function addChapter(Chapter $chapter): self
    {
        if (!$this->chapters->contains($chapter)) {
            $this->chapters[] = $chapter;
            $chapter->setFormation($this);
        }

        return $this;
    }

    public function removeChapter(Chapter $chapter): self
    {
        if ($this->chapters->removeElement($chapter)) {
            // set the owning side to null (unless already changed)
            if ($chapter->getFormation() === $this) {
                $chapter->setFormation(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|TrackFormation[]
     */
    public function getTrackformations(): Collection
    {
        return $this->trackformations;
    }

    public function addTrackformation(TrackFormation $trackformation): self
    {
        if (!$this->trackformations->contains($trackformation)) {
            $this->trackformations[] = $trackformation;
            $trackformation->setFormation($this);
        }

        return $this;
    }

    public function removeTrackformation(TrackFormation $trackformation): self
    {
        if ($this->trackformations->removeElement($trackformation)) {
            // set the owning side to null (unless already changed)
            if ($trackformation->getFormation() === $this) {
                $trackformation->setFormation(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

   

    /**
     * Get the value of image
     */ 
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set the value of image
     *
     * @return  self
     */ 
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get the value of language
     */ 
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set the value of language
     *
     * @return  self
     */ 
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get the value of skills
     */ 
    public function getSkills()
    {
        return $this->skills;
    }

    /**
     * Set the value of skills
     *
     * @return  self
     */ 
    public function setSkills($skills)
    {
        $this->skills = $skills;

        return $this;
    }

    /**
     * Get the value of updateDate
     */ 
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * Set the value of updateDate
     *
     * @return  self
     */ 
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * Get the value of document
     */ 
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set the value of document
     *
     * @return  self
     */ 
    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }
}
