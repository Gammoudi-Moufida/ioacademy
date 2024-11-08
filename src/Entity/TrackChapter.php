<?php

namespace App\Entity;

use App\Repository\TrackChapterRepository;
use Doctrine\ORM\Mapping as ORM;



#[ORM\Entity(repositoryClass: TrackChapterRepository::class)]
class TrackChapter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: TrackFormation::class, inversedBy: 'trackFormations')]
    private $trackFormations;

    #[ORM\ManyToOne(targetEntity: Chapter::class, inversedBy: 'trackchapter')]
    private $chapter;

    #[ORM\Column(type: 'boolean')]
    private $finished;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChapter()
    {
        return $this->chapter;
    }

    /**
     *
     * @return  self
     */ 
    public function setChapter($chapter)
    {
        $this->chapter = $chapter;

        return $this;
    }
 
    public function getFinished()
    {
        return $this->finished;
    }

    /**
     *
     * @return  self
     */ 
    public function setFinished($finished)
    {
        $this->finished = $finished;

        return $this;
    }

    public function getTrackFormations()
    {
        return $this->trackFormations;
    }

    /**
     *
     * @return  self
     */ 
    public function setTrackFormations($trackFormations)
    {
        $this->trackFormations = $trackFormations;

        return $this;
    }
}
