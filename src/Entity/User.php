<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
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

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilePicture = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: CourseProgress::class)]
    private Collection $courseProgresses;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: QuizResult::class)]
    private Collection $quizResults;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserInterest::class)]
    private Collection $interests;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public function __construct()
    {
        $this->courseProgresses = new ArrayCollection();
        $this->quizResults = new ArrayCollection();
        $this->interests = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): static
    {
        $this->profilePicture = $profilePicture;
        return $this;
    }

    /**
     * @return Collection<int, CourseProgress>
     */
    public function getCourseProgresses(): Collection
    {
        return $this->courseProgresses;
    }

    /**
     * @return Collection<int, QuizResult>
     */
    public function getQuizResults(): Collection
    {
        return $this->quizResults;
    }

    /**
     * @return Collection<int, UserInterest>
     */
    public function getInterests(): Collection
    {
        return $this->interests;
    }

    public function addInterest(UserInterest $interest): self
    {
        if (!$this->interests->contains($interest)) {
            $this->interests->add($interest);
            $interest->setUser($this);
        }
        return $this;
    }

    public function removeInterest(UserInterest $interest): self
    {
        if ($this->interests->removeElement($interest)) {
            if ($interest->getUser() === $this) {
                $interest->setUser(null);
            }
        }
        return $this;
    }

    // Méthodes requises par UserInterface
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Si vous stockez des données temporaires sensibles sur l'utilisateur, effacez-les ici
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function isAdmin(): bool
    {
        return in_array(self::ROLE_ADMIN, $this->getRoles());
    }

    public function setAdmin(bool $admin): self
    {
        if ($admin) {
            $this->roles[] = self::ROLE_ADMIN;
        } else {
            $this->roles = array_diff($this->roles, [self::ROLE_ADMIN]);
        }
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
} 