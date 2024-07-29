<?php

namespace App\Entity;

use App\Repository\TodoListRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TodoListRepository::class)]
#[ORM\HasLifecycleCallbacks]
class TodoList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Task = null;

    #[ORM\ManyToOne(inversedBy: 'todolist')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Conference $conference = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

  #[ORM\Column(options: ["default" => false])]
  private ?bool $isCompleted = null;

  public function __toString(): string
  {
    return (string) $this->getTask();
  }

  public function getId(): ?int
    {
        return $this->id;
    }

    public function getTask(): ?string
    {
        return $this->Task;
    }

    public function setTask(?string $Task): static
    {
        $this->Task = $Task;

        return $this;
    }

    public function getConference(): ?Conference
    {
        return $this->conference;
    }

    public function setConference(?Conference $conference): static
    {
        $this->conference = $conference;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isCompleted(): ?bool
    {
        return $this->isCompleted;
    }

    public function setIsCompleted(bool $isCompleted): static
    {
        $this->isCompleted = $isCompleted;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCompletedValue() {
      $this->isCompleted = false;
    }
}
