<?php


namespace App\Entity;


use App\Api\Action\BudgetRequest\Status;
use DateTime;

class BudgetRequest
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $title;

    /** @var string */
    protected $description;

    /** @var Category */
    protected $category;

    /** @var string */
    protected $status;

    /** @var User */
    protected $user;

    /** @var DateTime */
    protected $createdAt;

    /** @var DateTime */
    protected $updatedAt;


    public function __construct(?string $title, string $description, ?Category $category, User $user)
    {
        $this->title = $title;
        $this->description = $description;
        $this->category = $category;
        $this->status = Status::STATUS_PENDING;
        $this->user = $user;
        $this->createdAt = new DateTime();
        $this->markAsUpdated();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return Category
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function markAsUpdated(): void
    {
        $this->updatedAt =new DateTime();
    }
}