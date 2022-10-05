<?php

namespace App\Entity;

use App\Repository\ReactionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReactionRepository::class)]
class Reaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: false)]
    private ?bool $likes = false;

    #[ORM\Column(nullable: false)]
    private ?bool $loves = false;

    #[ORM\Column(nullable: false)]
    private ?bool $funny = false;

    #[ORM\Column(nullable: false)]
    private ?bool $interested = false;

    #[ORM\Column(nullable: false)]
    private ?bool $dontLike = false;

    #[ORM\Column(nullable: false)]
    private ?bool $angry = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isLikes(): ?bool
    {
        return $this->likes;
    }

    public function setLikes(?bool $likes): self
    {
        $this->likes = $likes;

        return $this;
    }

    public function isLoves(): ?bool
    {
        return $this->loves;
    }

    public function setLoves(?bool $loves): self
    {
        $this->loves = $loves;

        return $this;
    }

    public function isFunny(): ?bool
    {
        return $this->funny;
    }

    public function setFunny(?bool $funny): self
    {
        $this->funny = $funny;

        return $this;
    }

    public function isInterested(): ?bool
    {
        return $this->interested;
    }

    public function setInterested(?bool $interested): self
    {
        $this->interested = $interested;

        return $this;
    }

    public function isDontLike(): ?bool
    {
        return $this->dontLike;
    }

    public function setDontLike(?bool $dontLike): self
    {
        $this->dontLike = $dontLike;

        return $this;
    }

    public function isAngry(): ?bool
    {
        return $this->angry;
    }

    public function setAngry(?bool $angry): self
    {
        $this->angry = $angry;

        return $this;
    }
}
