<?php


namespace App\Entity;


use DateTime;

class Category
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var DateTime */
    private $createdAt;

    public function __construct(string $name, ?string $description)
    {
        $this->name = $name;
        $this->description = $description;
        $this->createdAt = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
}