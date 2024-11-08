<?php

namespace App\Entity;

use App\Repository\ChapterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ChapterRepository::class)]
class Chapter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Assert\NotBlank(message:"Titre requis !")]
    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[Assert\NotBlank(message:"Description requis !")]
    #[ORM\Column(type: 'text')]
    private $description;

    #[ORM\Column(type: 'boolean')]
    private $active=0;

    #[ORM\Column(type: 'date')]
    private $publicationDate;

    #[ORM\Column(type: 'integer', nullable:true)]
    #[Assert\NotBlank(message:"Order d'affichage requis !")]
    #[Assert\Range(min: 1 , minMessage: 'Cette valeur doit être 1 ou plus.', )]
    private $orderChapter;

    #[ORM\OneToMany(mappedBy: 'chapter', targetEntity: Blog::class)]
    #[ORM\OrderBy(["id" => "ASC"])]
    private $blogs;

    #[ORM\ManyToOne(targetEntity: Formation::class, inversedBy: 'chapters')]
    #[ORM\JoinColumn(nullable: false)]
    private $formation;

    #[ORM\OneToMany(mappedBy: 'chapter', targetEntity: TrackChapter::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $trackchapter;

    public function __construct()
    {
        $this->blogs = new ArrayCollection();
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

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
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
     * @return Collection|Blog[]
     */
    public function getBlogs(): Collection
    {
        return $this->blogs;
    }

    public function addBlog(Blog $blog): self
    {
        if (!$this->blogs->contains($blog)) {
            $this->blogs[] = $blog;
            $blog->setChapter($this);
        }

        return $this;
    }

    public function removeBlog(Blog $blog): self
    {
        if ($this->blogs->removeElement($blog)) {
            // set the owning side to null (unless already changed)
            if ($blog->getChapter() === $this) {
                $blog->setChapter(null);
            }
        }

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): self
    {
        $this->formation = $formation;

        return $this;
    }

   

  

    /**
     * Get the value of trackchapter
     */ 
    public function getTrackchapter()
    {
        return $this->trackchapter;
    }

    /**
     * Set the value of trackchapter
     *
     * @return  self
     */ 
    public function setTrackchapter($trackchapter)
    {
        $this->trackchapter = $trackchapter;

        return $this;
    }
    
    /**
     * Get the value of orderChapter
     */ 
    public function getOrderChapter()
    {
        return $this->orderChapter;
    }

    /**
     * Set the value of orderChapter
     *
     * @return  self
     */ 
    public function setOrderChapter($orderChapter)
    {
        $this->orderChapter = $orderChapter;

        return $this;
    }
}
