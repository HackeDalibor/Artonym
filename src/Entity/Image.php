<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // TODO :
    // #[Assert\Image(
    //     detectCorrupted: true,
    //     corruptedMessage: 'The image file is corrupted.',
    //     minWidth: 200,
    //     minWidthMessage: 'The image width is too small ({{ width }}px). Minimum width expected is {{ min_width }}px.',
    //     maxWidth: 1500,
    //     maxWidthMessage: 'The image width is too big ({{ width }}px). Allowed maximum width is {{ max_width }}px',
    //     minHeight: 200,
    //     minHeightMessage: 'The image height is too small ({{ height }}px). Minimum height expected is {{ min_height }}px.',
    //     maxHeight: 1500,
    //     maxHeightMessage: 'The image height is too big ({{ height }}px). Allowed maximum height is {{ max_height }}px.',
    //     sizeNotDetectedMessage: 'The size of the image could not be detected.',
    //     mimeTypes: [
    //         'image/png',
    //         'image/jpeg',
    //         'image/jpg',
    //         'image/gif',
    //     ],
    //     mimeTypesMessage: 'The file type of the file is invalid ({{ type }}). Allowed file types are {{ types }}.',
    // )]
    #[ORM\Column(length: 255)]
    private ?string $data = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?Subject $subject = null;

    #[ORM\OneToOne(inversedBy: 'image', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(string $data): self
    {
        $this->data = $data;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    // public static function loadValidatorMetadata(ClassMetadata $metadata)
    // {
    //     $metadata->addPropertyConstraint('data', new Assert\Image([
    //         'detectCorrupted' => true,
    //         'corruptedMessage' => 'The image file is corrupted.',
            // 'minWidth' => 200,
            // 'minWidthMessage' => 'he image width is too small ({{ width }}px).
            // Minimum width expected is {{ min_width }}px.',
            // 'maxWidth' => 1500,
            // 'maxWidthMessage' => 'The image width is too big ({{ width }}px).
            // Allowed maximum width is {{ max_width }}px',
            // 'minHeight' => 200,
            // 'minHeightMessage' => 'The image height is too small ({{ height }}px).
            // Minimum height expected is {{ min_height }}px.',
            // 'maxHeight' => 1500,
            // 'maxHeightMessage' => 'The image height is too big ({{ height }}px).
            // Allowed maximum height is {{ max_height }}px.',
            // 'sizeNotDetectedMessage' => 'The size of the image could not be detected.',
            // 'mimeTypes' => [
            //     'image/png',
            //     'image/jpeg',
            //     "image/jpg",
            //     'image/gif',
            // ],
            // 'mimeTypesMessage' => 'The file type of the file is invalid ({{ type }}). Allowed file types are {{ types }}.',
    //     ]));
    // }

    public function __toString()
    {
        return $this->data;
    }
}
