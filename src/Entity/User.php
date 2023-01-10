<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\Post;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    operations: [
        new Post(denormalizationContext: ['groups' => 'user:add']),
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    const ROLE_ADMIN = "ROLE_ADMIN";
    const DEFAULT_ROLES = [self::ROLE_ADMIN];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:add'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:add'])]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:add'])]
    private ?string $password = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY)]
    private array $roles = [];

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Read::class)]
    private Collection $ureads;

    public function __construct()
    {
        $this->ureads = new ArrayCollection();
        $this->roles = self::DEFAULT_ROLES;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection<int, Read>
     */
    public function getUreads(): Collection
    {
        return $this->ureads;
    }

    public function addUread(Read $uread): self
    {
        if (!$this->ureads->contains($uread)) {
            $this->ureads->add($uread);
            $uread->setUser($this);
        }

        return $this;
    }

    public function removeUread(Read $uread): self
    {
        if ($this->ureads->removeElement($uread)) {
            // set the owning side to null (unless already changed)
            if ($uread->getUser() === $this) {
                $uread->setUser(null);
            }
        }

        return $this;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

}
