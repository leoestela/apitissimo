<?php


namespace App\Entity;


use App\Api\Action\BudgetRequest\Status;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BudgetRequestRepository")
 * @ORM\Table(name="budget_request")
 */
class BudgetRequest
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", length=50)
     * @Assert\NotBlank
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=500)
     * @Assert\NotBlank
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="budgetRequests")
     */
    protected $category;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank
     * * @Assert\Choice({Status::STATUS_DISCARDED, Status::STATUS_PENDING, Status::STATUS_PUBLISHED})
     */
    protected $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="budgetRequests")
     * @Assert\NotBlank
     */
    protected $user;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     * @Assert\NotBlank
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", name="updated_at")
     * @Assert\NotBlank
     */
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

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getUser(): User
    {
        return $this->user;
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
        $this->updatedAt = new DateTime();
    }
}