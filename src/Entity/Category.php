<?php


namespace App\Entity;


use App\Message\Message;
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
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200)
     * @Assert\NotBlank
     * @Assert\Length(
     *      max = 200,
     *      maxMessage = Message::CATEGORY_NAME_MAX_LENGTH)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=500)
     * @Assert\Length(
     *      max = 500,
     *      maxMessage = Message::CATEGORY_DESCRIPTION_MAX_LENGTH)
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

    public function serialize(): array
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s')
        );
    }
}