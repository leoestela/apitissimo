<?php


namespace App\Entity;


use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="user")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", length=50)
     * @Assert\NotBlank
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=200)
     * @Assert\NotBlank
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     */
    protected $email;

    /**
     * @ORM\Column(type="integer", length=20)
     * @Assert\NotBlank
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", length=500)
     * @Assert\NotBlank
     */
    protected $address;

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

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BudgetRequest", mappedBy="user")
     */
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

    public function getPhone(): int
    {
        return $this->phone;
    }

    public function setPhone(int $phone): void
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

    public function getBudgetRequests(): ?Collection
    {
        return $this->budgetRequests;
    }
}