<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[MongoDB\Document(db: "innoDeco_db", collection: "users")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[MongoDB\Id]
    private $id;
    #[MongoDB\Field(type: Type::STRING)]
    private $firstName;

    #[MongoDB\Field(type: Type::STRING)]
    private $lastName;

    #[MongoDB\Field(type: Type::STRING)]
    private $username;

    #[MongoDB\Field(type: Type::STRING)]
    private $password;

    #[MongoDB\Field(type: Type::DATE)]
    private $birthDate;

    #[MongoDB\Field(type: Type::STRING)]
    private $gender;

    #[MongoDB\Field(type: Type::STRING)]
    private $role;

   

    // Getters et setters pour les propriétés

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }
    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthdaydate): void
    {
        $this->birthDate = $birthdaydate;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): void
    {
        $this->gender = $gender;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function getRoles(): array
    {
        return [$this->role];
    }

    public function eraseCredentials()
    {
        // Si vous avez besoin de supprimer des informations sensibles de l'utilisateur, faites-le ici
    }

    public function getUserIdentifier(): string
    {
        return $this->username; // Retourne l'identifiant de l'utilisateur, qui peut être l'email par exemple
    }
}
