<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\ConferenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\String\Slugger\SluggerInterface;

#[ORM\Entity(repositoryClass: ConferenceRepository::class)]
#[UniqueEntity('slug')]
#[ApiResource(
  operations: [
    new Get(normalizationContext: ['groups' => 'conference:item']),
    new GetCollection(normalizationContext: ['groups' => 'conference:list']),
  ],
  order: ['year' => 'DESC', 'city' => 'ASC'],
  paginationEnabled: false,
)]
class Conference implements AccountEntityAwareInterface
{

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  #[Groups(['conference:list', 'conference:item'])]
  private ?int $id = NULL;

  #[ORM\Column(length: 255)]
  #[Groups(['conference:list', 'conference:item'])]
  private ?string $city = NULL;

  #[ORM\Column(length: 4)]
  #[Groups(['conference:list', 'conference:item'])]
  private ?string $year = NULL;

  #[ORM\Column]
  #[Groups(['conference:list', 'conference:item'])]
  private ?bool $isInternational = NULL;

  #[ORM\Column(length: 255, unique: TRUE)]
  #[Groups(['conference:list', 'conference:item'])]
  private ?string $slug = NULL;

  /**
   * @var Collection<int, Comment>
   */
  #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'conference', orphanRemoval: TRUE)]
  private Collection $comments;

  /**
   * @var Collection<int, TodoList>
   */
  #[ORM\OneToMany(targetEntity: TodoList::class, mappedBy: 'conference', orphanRemoval: TRUE)]
  private Collection $todolist;

  #[ORM\ManyToOne(inversedBy: 'conference')]
  private ?AccountEntity $accountEntity = null;

  public function __construct() {
    $this->comments = new ArrayCollection();
    $this->todolist = new ArrayCollection();
  }

  public function __toString(): string {
    return $this->city . ' ' . $this->year;
  }

  public function getId(): ?int {
    return $this->id;
  }

  public function computeSlug(SluggerInterface $slugger): void {
    if (!$this->slug || '-' === $this->slug) {
      $this->slug = (string) $slugger->slug((string) $this)->lower();
    }
  }

  public function getCity(): ?string {
    return $this->city;
  }

  public function setCity(string $city): static {
    $this->city = $city;

    return $this;
  }

  public function getYear(): ?string {
    return $this->year;
  }

  public function setYear(string $year): static {
    $this->year = $year;

    return $this;
  }

  public function isInternational(): ?bool {
    return $this->isInternational;
  }

  public function setIsInternational(bool $isInternational): static {
    $this->isInternational = $isInternational;

    return $this;
  }

  /**
   * @return Collection<int, Comment>
   */
  public function getComments(): Collection {
    return $this->comments;
  }

  public function addComment(Comment $comment): static {
    if (!$this->comments->contains($comment)) {
      $this->comments->add($comment);
      $comment->setConference($this);
    }

    return $this;
  }

  public function removeComment(Comment $comment): static {
    if ($this->comments->removeElement($comment)) {
      // set the owning side to null (unless already changed)
      if ($comment->getConference() === $this) {
        $comment->setConference(NULL);
      }
    }
    return $this;
  }

  public function getSlug(): ?string {
    return $this->slug;
  }

  public function setSlug(string $slug): static {
    $this->slug = $slug;

    return $this;
  }

  /**
   * @return Collection<int, TodoList>
   */
  public function getTodoList(): Collection {
    return $this->todolist;
  }

  public function getAccountEntity(): ?AccountEntity
  {
      return $this->accountEntity;
  }

  public function setAccountEntity(?AccountEntity $accountEntity): static
  {
      $this->accountEntity = $accountEntity;

      return $this;
  }

}
//
//    public function addTodoList(TodoList $todolist): static
//    {
//        if (!$this->todolist->contains($todolist)) {
//            $this->todolist>add($todolist);
//            $todolist->setConference($this);
//        }
//
//        return $this;
//    }
//
//    public function removeTodoList(TodoList $todolist): static
//    {
//        if ($this->todolist->removeElement($todolist)) {
//            // set the owning side to null (unless already changed)
//            if ($todolist->getConference() === $this) {
//                $todolist->setConference(null);
//            }
//        }
//
//        return $this;
//    }
//}
