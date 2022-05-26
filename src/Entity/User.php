<?php

namespace App\Entity;

use App\Entity\Exception\InvalidRoleException;
use App\Entity\Utils\TimestampTrait;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Rules\Properties\ReadWritePropertiesExtension;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'app_user')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampTrait;

    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public const ROLE_DEFAULT = self::ROLE_USER;

    public const ROLES = [
        self::ROLE_USER,
        self::ROLE_ADMIN
    ];

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $username;

    /** @var array<string> */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private string $password;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function __toString(): string
    {
        return $this->getUserIdentifier();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = self::ROLE_DEFAULT;

        return array_unique($roles);
    }

    /**
     * @throws InvalidRoleException
     */
    public function addRole(string $roleString): self
    {
        if (in_array($roleString, self::ROLES, true)) {
            $this->roles[] = $roleString;

            return $this;
        }

        throw InvalidRoleException::createFromUserAndRole($this, $roleString);
    }

    public function hasRole(string $wantedRole): bool
    {
        return in_array($wantedRole, $this->roles, true);
    }

    public function removeRole(string $roleString): self
    {
        $this->roles = array_filter($this->roles, static function ($value) use ($roleString) {
            return $value !== $roleString;
        });

        return $this;
    }

    /** @param array<string> $roles */
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
    public function eraseCredentials(): void
    {
    }
}