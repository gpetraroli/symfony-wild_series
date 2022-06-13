<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Comment::class)]
    private $comments;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Program::class)]
    private $programs;

    #[ORM\ManyToMany(targetEntity: Program::class, mappedBy: 'viewers')]
    #[ORM\JoinTable(name: 'watchlist')]
    private $watchlist;

    /**
     * @return mixed
     */
    public function getPrograms()
    {
        return $this->programs;
    }

    /**
     * @param mixed $programs
     */
    public function setPrograms($programs): void
    {
        $this->programs = $programs;
    }

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->watchlist = new ArrayCollection();
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
            $this->comments[] = $comment;
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

    /**
     * @return Collection<int, Program>
     */
    public function getWatchlist(): Collection
    {
        return $this->watchlist;
    }

    public function addToWatchlist(Program $watchlist): self
    {
        if (!$this->watchlist->contains($watchlist)) {
            $this->watchlist[] = $watchlist;
            $watchlist->addViewer($this);
        }

        return $this;
    }

    public function removeFromWatchlist(Program $watchlist): self
    {
        if ($this->watchlist->removeElement($watchlist)) {
            $watchlist->removeViewer($this);
        }

        return $this;
    }

    public function isInWatchlist(Program $program): bool
    {
        return $this->getWatchlist()->contains($program);
    }
}
