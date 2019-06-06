<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TodoListRepository")
 * @ORM\HasLifecycleCallbacks
 */
class TodoList
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     *
     * @Assert\NotBlank(message="This value should not be blank.")
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TodoItem", mappedBy="list", orphanRemoval=true, fetch="EAGER")
     * @Groups({"items"})
     */
    private $items;

    /**
     * @var int
     */
    private $items_count;

    /**
     * TodoList constructor.
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return TodoList
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return Collection|TodoItem[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * @param TodoItem $item
     *
     * @return TodoList
     */
    public function addItem(TodoItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setList($this);
        }

        return $this;
    }

    /**
     * @param TodoItem $item
     *
     * @return TodoList
     */
    public function removeItem(TodoItem $item): self
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            // set the owning side to null (unless already changed)
            if ($item->getList() === $this) {
                $item->setList(null);
            }
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getItemsCount(): int
    {
        return $this->items_count;
    }

    /**
     * @ORM\PostLoad
     */
    public function setItemsCount(): void
    {
        // get collection size after entity load
        $this->items_count = $this->items->count();
    }


}

