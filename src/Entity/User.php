<?php


namespace App\Entity;


use DateTime;
use Doctrine\Common\Collections\Collection;

class User
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $email;

    /** @var string */
    protected $phone;

    /** @var string */
    protected $address;

    /** @var DateTime */
    protected $createdAt;

    /** @var DateTime */
    protected $updatedAt;

    /** @var null|Collection|BudgetRequest[] */
    protected $budgetRequests = null;


    public function __construct(string $email, string $phone, string $address)
    {
        $this->email = $email;
        $this->phone = $phone;
        $this->address = $address;
        $this->createdAt = new DateTime();
        $this->markAsUpdated();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function markAsUpdated(): void
    {
        $this->updatedAt =new DateTime();
    }

    public function getBudgetRequests(): Collection
    {
        return $this->budgetRequests;
    }
}