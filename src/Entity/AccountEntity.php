<?php

namespace App\Entity;

use App\Repository\AccountEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountEntityRepository::class)]
class AccountEntity
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $accountNumber = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $Name = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $location = null;

  /**
   * @var Collection<int, Admin>
   */
  #[ORM\OneToMany(targetEntity: Admin::class, mappedBy: 'accountEntity', orphanRemoval: true)]
  private Collection $users;

  #[ORM\Column(length: 255, unique: true)]
  private ?string $organisationNumber = null;

  public function __construct()
  {
    $this->users = new ArrayCollection();
  }

  public function __toString() : string
  {
    return $this->getAccountNumber() ?? '';
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getAccountNumber(): ?string
  {
    return $this->accountNumber;
  }

  public function setAccountNumber(?string $accountNumber): static
  {
    $this->accountNumber = $accountNumber;
    return $this;
  }

  public function getName(): ?string
  {
    return $this->Name;
  }

  public function setName(?string $Name): static
  {
    $this->Name = $Name;
    return $this;
  }

  public function getLocation(): ?string
  {
    return $this->location;
  }

  public function setLocation(?string $location): static
  {
    $this->location = $location;
    $this->updatedAccountNumber();
    return $this;
  }

  /**
   * @return Collection<int, Admin>
   */
  public function getUsers(): Collection
  {
    return $this->users;
  }

  public function addUser(Admin $user): static
  {
    if (!$this->users->contains($user)) {
      $this->users->add($user);
      $user->setAccountEntity($this);
    }
    return $this;
  }

  public function removeUser(Admin $user): static
  {
    if ($this->users->removeElement($user)) {
      if ($user->getAccountEntity() === $this) {
        $user->setAccountEntity(null);
      }
    }
    return $this;
  }

  public function getOrganisationNumber(): ?string
  {
    return $this->organisationNumber;
  }

  public function setOrganisationNumber(string $organisationNumber): static
  {
    $this->organisationNumber = $organisationNumber;
    $this->updatedAccountNumber();

    return $this;
  }

  private function updatedAccountNumber(): void
  {
    $this->accountNumber = $this->location . $this->organisationNumber;
  }
}
