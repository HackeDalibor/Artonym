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

    #[ORM\Column]
    private ?bool $likes = false;

    #[ORM\ManyToOne(inversedBy: 'reactions')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'reactions')]
    private ?Subject $subject = null;

    #[ORM\Column]
    private ?bool $loves = false;

    #[ORM\Column]
    private ?bool $dontLike = false;

    #[ORM\Column]
    private ?bool $wow = false;

    #[ORM\Column]
    private ?bool $funny = false;

    #[ORM\Column]
    private ?bool $sad = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isLikes(): ?bool
    {
        return $this->likes;
    }

    public function setLikes(bool $likes): self
    {
        $this->likes = $likes;

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

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function isLoves(): ?bool
    {
        return $this->loves;
    }

    public function setLoves(bool $loves): self
    {
        $this->loves = $loves;

        return $this;
    }

    public function isDontLike(): ?bool
    {
        return $this->dontLike;
    }

    public function setDontLike(bool $dontLike): self
    {
        $this->dontLike = $dontLike;

        return $this;
    }

    public function isWow(): ?bool
    {
        return $this->wow;
    }

    public function setWow(bool $wow): self
    {
        $this->wow = $wow;

        return $this;
    }

    public function isFunny(): ?bool
    {
        return $this->funny;
    }

    public function setFunny(bool $funny): self
    {
        $this->funny = $funny;

        return $this;
    }

    public function isSad(): ?bool
    {
        return $this->sad;
    }

    public function setSad(bool $sad): self
    {
        $this->sad = $sad;

        return $this;
    }
}
