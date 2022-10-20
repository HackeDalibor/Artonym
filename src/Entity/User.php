<?php

namespace App\Entity;

use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
// #[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $nickname = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $inscriptionDate = null;

    #[ORM\Column]
    private ?bool $status = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Subject::class)]
    private Collection $subjects;

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $sentMessages;

    #[ORM\OneToMany(mappedBy: 'reciever', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $recievedMessages;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Image $image = null;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'following')]
    private Collection $followers;

    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'followers')]
    private Collection $following;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Notification::class, orphanRemoval: true)]
    private Collection $notifications;

    #[ORM\ManyToMany(targetEntity: Subject::class, mappedBy: 'likedBy')]
    private Collection $likedSubjects;

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: DirectMessage::class)]
    private Collection $sentDirectMessages;

    #[ORM\OneToMany(mappedBy: 'reciever', targetEntity: DirectMessage::class)]
    private Collection $recievedDirectMessages;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Reaction::class)]
    private Collection $reactions;

    public function __construct()
    {
        $this->subjects = new ArrayCollection();
        $this->sentMessages = new ArrayCollection();
        $this->recievedMessages = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->inscriptionDate = new DateTime("now", new DateTimeZone('Europe/Paris'));
        $this->status = false;
        $this->followers = new ArrayCollection();
        $this->following = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->likedSubjects = new ArrayCollection();
        $this->sentDirectMessages = new ArrayCollection();
        $this->recievedDirectMessages = new ArrayCollection();
        $this->reactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getInscriptionDate(): string
    {
        return $this->inscriptionDate->format("d/m/Y");
    }

    public function setInscriptionDate(\DateTimeInterface $inscriptionDate): self
    {
        $this->inscriptionDate = $inscriptionDate;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Subject>
     */
    public function getSubjects(): Collection
    {
        return $this->subjects;
    }

    public function addSubject(Subject $subject): self
    {
        if (!$this->subjects->contains($subject)) {
            $this->subjects->add($subject);
            $subject->setUser($this);
        }

        return $this;
    }

    public function removeSubject(Subject $subject): self
    {
        if ($this->subjects->removeElement($subject)) {
            // set the owning side to null (unless already changed)
            if ($subject->getUser() === $this) {
                $subject->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getSentMessages(): Collection
    {
        return $this->sentMessages;
    }

    public function addSentMessage(Message $sentMessage): self
    {
        if (!$this->sentMessages->contains($sentMessage)) {
            $this->sentMessages->add($sentMessage);
            $sentMessage->setSender($this);
        }

        return $this;
    }

    public function removeSentMessage(Message $sentMessage): self
    {
        if ($this->sentMessages->removeElement($sentMessage)) {
            // set the owning side to null (unless already changed)
            if ($sentMessage->getSender() === $this) {
                $sentMessage->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getRecievedMessages(): Collection
    {
        return $this->recievedMessages;
    }

    public function addRecievedMessage(Message $recievedMessage): self
    {
        if (!$this->recievedMessages->contains($recievedMessage)) {
            $this->recievedMessages->add($recievedMessage);
            $recievedMessage->setReciever($this);
        }

        return $this;
    }

    public function removeRecievedMessage(Message $recievedMessage): self
    {
        if ($this->recievedMessages->removeElement($recievedMessage)) {
            // set the owning side to null (unless already changed)
            if ($recievedMessage->getReciever() === $this) {
                $recievedMessage->setReciever(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        // unset the owning side of the relation if necessary
        if ($image === null && $this->image !== null) {
            $this->image->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($image !== null && $image->getUser() !== $this) {
            $image->setUser($this);
        }

        $this->image = $image;

        return $this;
    }
    
    /**
     * @return Collection<int, self>
     */
    public function getFollowers(): Collection
    {
        return $this->followers;
    }
    
    public function addFollower(self $follower): self
    {
        if (!$this->followers->contains($follower)) {
            $this->followers->add($follower);
        }

        return $this;
    }
    
    public function removeFollower(self $follower): self
    {
        $this->followers->removeElement($follower);
        
        return $this;
    }
    
    /**
     * @return Collection<int, self>
     */
    public function getFollowing(): Collection
    {
        return $this->following;
    }
    
    public function addFollowing(self $following): self
    {
        if (!$this->following->contains($following)) {
            $this->following->add($following);
            $following->addFollower($this);
        }
        
        return $this;
    }
    
    public function removeFollowing(self $following): self
    {
        if ($this->following->removeElement($following)) {
            $following->removeFollower($this);
        }
        
        return $this;
    }
    
    public function __toString()
    {
        return $this->nickname;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Subject>
     */
    public function getLikedSubjects(): Collection
    {
        return $this->likedSubjects;
    }

    public function addLikedSubject(Subject $likedSubject): self
    {
        if (!$this->likedSubjects->contains($likedSubject)) {
            $this->likedSubjects->add($likedSubject);
            $likedSubject->addLikedBy($this);
        }

        return $this;
    }

    public function removeLikedSubject(Subject $likedSubject): self
    {
        if ($this->likedSubjects->removeElement($likedSubject)) {
            $likedSubject->removeLikedBy($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, DirectMessage>
     */
    public function getSentDirectMessages(): Collection
    {
        return $this->sentDirectMessages;
    }

    public function addSentDirectMessage(DirectMessage $sentDirectMessage): self
    {
        if (!$this->sentDirectMessages->contains($sentDirectMessage)) {
            $this->sentDirectMessages->add($sentDirectMessage);
            $sentDirectMessage->setSender($this);
        }

        return $this;
    }

    public function removeSentDirectMessage(DirectMessage $sentDirectMessage): self
    {
        if ($this->sentDirectMessages->removeElement($sentDirectMessage)) {
            // set the owning side to null (unless already changed)
            if ($sentDirectMessage->getSender() === $this) {
                $sentDirectMessage->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DirectMessage>
     */
    public function getRecievedDirectMessages(): Collection
    {
        return $this->recievedDirectMessages;
    }

    public function addRecievedDirectMessage(DirectMessage $recievedDirectMessage): self
    {
        if (!$this->recievedDirectMessages->contains($recievedDirectMessage)) {
            $this->recievedDirectMessages->add($recievedDirectMessage);
            $recievedDirectMessage->setReciever($this);
        }

        return $this;
    }

    public function removeRecievedDirectMessage(DirectMessage $recievedDirectMessage): self
    {
        if ($this->recievedDirectMessages->removeElement($recievedDirectMessage)) {
            // set the owning side to null (unless already changed)
            if ($recievedDirectMessage->getReciever() === $this) {
                $recievedDirectMessage->setReciever(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reaction>
     */
    public function getReactions(): Collection
    {
        return $this->reactions;
    }

    public function addReaction(Reaction $reaction): self
    {
        if (!$this->reactions->contains($reaction)) {
            $this->reactions->add($reaction);
            $reaction->setUser($this);
        }

        return $this;
    }

    public function removeReaction(Reaction $reaction): self
    {
        if ($this->reactions->removeElement($reaction)) {
            // set the owning side to null (unless already changed)
            if ($reaction->getUser() === $this) {
                $reaction->setUser(null);
            }
        }

        return $this;
    }
}
