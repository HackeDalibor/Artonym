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
    private ?bool $likes = null;

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
}
