<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\TodoItemRepository")
 * @ORM\HasLifecycleCallbacks
 */
class TodoItem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TodoList", inversedBy="items")
     * @ORM\JoinColumn(nullable=false)
     */
    private $list;

    /**
     * @ORM\Column(type="string", length=50)
     *
     * @Assert\NotBlank(message="This value should not be blank.")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return TodoList|null
     */
    public function getList(): ?TodoList
    {
        return $this->list;
    }

    /**
     * @param TodoList|null $list
     *
     * @return TodoItem
     */
    public function setList(?TodoList $list): self
    {
        $this->list = $list;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return TodoItem
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

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
    public function setCreatedAtValue(): self
    {
        $this->createdAt = new \DateTime();

        return $this;
    }
}

