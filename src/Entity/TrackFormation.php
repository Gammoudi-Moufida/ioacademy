<?php

namespace App\Entity;

use App\Repository\TrackFormationRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: TrackFormationRepository::class)]
class TrackFormation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $startTime;

    #[ORM\Column(type: 'datetime', nullable:true)]
    private $endTime;

    #[ORM\Column(type:"string", length:10)]
    private $status = 'started';

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'trackFormations')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\OneToMany(mappedBy: 'trackFormations', targetEntity: TrackChapter::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $chapters;

    // #[ORM\ManyToOne(targetEntity: Formation::class, inversedBy: 'trackformations', fetch:'EAGER')]
    #[ORM\ManyToOne(targetEntity: Formation::class, inversedBy: 'trackformations', fetch:'LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    private $formation;
    
    public function __construct()
    {
        $this->chapters = new ArrayCollection();
    }
   
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeInterface $endTime)
    {
        $this->endTime=$endTime;
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
     * Get the list of chapters
     */ 
   /**
     * @return Collection|Chapter[]
     */
    public function getTrackChapters(): Collection
    {
        return $this->chapters;
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
}
