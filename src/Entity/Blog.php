<?php

namespace App\Entity;

use App\Repository\BlogRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BlogRepository::class)]
class Blog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Assert\NotBlank(message: "Titre requis !")]
    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[Assert\NotBlank(message: "Champ requis !")]
    #[ORM\Column(type: 'text')]
    private $content;

    #[ORM\Column(type: 'boolean')]
    private $active = 0;

    #[ORM\Column(type: 'date')]
    private $publicationDate;

    #[ORM\Column(type: 'integer', nullable:true)]
    #[Assert\NotBlank(message:"Order d'affichage requis !")]
    #[Assert\Range(min: 1, minMessage: 'Cette valeur doit être 1 ou plus.',)]
    private $orderBlog;

    #[ORM\ManyToOne(targetEntity: Chapter::class, inversedBy: 'blogs')]
    #[ORM\JoinColumn(nullable: false)]
    private $chapter;




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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function getChapter(): ?Chapter
    {
        return $this->chapter;
    }

    public function setChapter(?Chapter $chapter): self
    {
        $this->chapter = $chapter;

        return $this;
    }

    /**
     * Get the value of orderBlog
     */ 
    public function getOrderBlog()
    {
        return $this->orderBlog;
    }

    /**
     * Set the value of orderBlog
     *
     * @return  self
     */ 
    public function setOrderBlog($orderBlog)
    {
        $this->orderBlog = $orderBlog;

        return $this;
    }
}
