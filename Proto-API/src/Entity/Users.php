<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_user = null;

    #[ORM\Column(length: 255)]
    private ?string $name_user = null;

    #[ORM\Column(length: 255)]
    private ?string $login_user = null;

    #[ORM\Column(length: 255)]
    private ?string $pass_user = null;

    #[ORM\Column(length: 255)]
    private ?string $mail_user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $last_connexion = null;

    #[ORM\Column]
    private ?int $type_compte = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(int $id_user): self
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getNameUser(): ?string
    {
        return $this->name_user;
    }

    public function setNameUser(string $name_user): self
    {
        $this->name_user = $name_user;

        return $this;
    }

    public function getLoginUser(): ?string
    {
        return $this->login_user;
    }

    public function setLoginUser(string $login_user): self
    {
        $this->login_user = $login_user;

        return $this;
    }

    public function getPassUser(): ?string
    {
        return $this->pass_user;
    }

    public function setPassUser(string $pass_user): self
    {
        $this->pass_user = $pass_user;

        return $this;
    }

    public function getMailUser(): ?string
    {
        return $this->mail_user;
    }

    public function setMailUser(string $mail_user): self
    {
        $this->mail_user = $mail_user;

        return $this;
    }

    public function getLastConnexion(): ?\DateTimeInterface
    {
        return $this->last_connexion;
    }

    public function setLastConnexion(\DateTimeInterface $last_connexion): self
    {
        $this->last_connexion = $last_connexion;

        return $this;
    }

    public function getTypeCompte(): ?int
    {
        return $this->type_compte;
    }

    public function setTypeCompte(int $type_compte): self
    {
        $this->type_compte = $type_compte;

        return $this;
    }
}
