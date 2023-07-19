<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
class Note
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $value = null;

    #[ORM\Column]
    private ?int $validate = null;
    
    #[ORM\Column]
    private ?int $nb_tentative = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $user_id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Course $course_id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $given_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getValidate(): ?int
    {
        return $this->validate;
    }

    public function setValidate(int $validate): static
    {
        $this->validate = $validate;

        return $this;
    }

    public function getNbTentative(): ?int
    {
        return $this->nb_tentative;
    }

    public function setNbTentative(int $nb_tentative): static
    {
        $this->nb_tentative = $nb_tentative;

        return $this;
    }

    public function getUserId(): ?Users
    {
        return $this->user_id;
    }

    public function setUserId(?Users $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getCourseId(): ?Course
    {
        return $this->course_id;
    }

    public function setCourseId(?Course $course_id): static
    {
        $this->course_id = $course_id;

        return $this;
    }

    public function getGivenAt(): ?\DateTimeInterface
    {
        return $this->given_at;
    }

    public function setGivenAt(\DateTimeInterface $given_at): static
    {
        $this->given_at = $given_at;

        return $this;
    }
}
