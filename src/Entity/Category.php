<?php


namespace App\Entity;


use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 * @ORM\Table(name="category")
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", length=50)
     * @Assert\NotBlank
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     * @Assert\NotBlank
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BudgetRequest", mappedBy="category")
     */
    protected $budgetRequests = null;


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

    public function getBudgetRequests(): ?Collection
    {
        return $this->budgetRequests;
    }
}