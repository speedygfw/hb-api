<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ContractRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Cascade;

#[ApiResource(
    //collectionOperations: ['get' => ['normalization_context' => ['groups' => 'bookings:list'] ]],
    // itemOperations: ['get' => ['normalization_context' => ['groups' => 'booking:item']]],
    paginationEnabled: false,
    normalizationContext: ['groups' => 'read', "datetime_format"=>"Y-m-d",],
    denormalizationContext: ['groups' => 'write', "datetime_format" => "Y-m-d|"]
)]
#[ORM\Entity(repositoryClass: ContractRepository::class)]
class Contract
{
    public const INCOME = 0;

    public const OUTCOME = 1;

    public const ROT_MONTHLY = 0;

    public const ROT_TWOMONTHLY = 1;

    public const ROT_QUATERLY = 2;

    public const ROT_HALFYEARLY = 3;

    public const ROT_YEARLY = 4;

    #[Groups(['read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'smallint')]
    private $rotation;

    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'date')]
    private $startDate;

    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'float')]
    private $amount;

    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'smallint')]
    private $type;

    #[Groups(['read', 'write'])]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'contracts')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: 'date', nullable: true)]
    private $lastAccomplished;

    #[Groups(['read', 'write'])]
    #[ORM\ManyToMany(
        targetEntity: Category::class,
        inversedBy: 'contracts',
        cascade: ['persist']
    )]
    private $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRotation(): ?int
    {
        return $this->rotation;
    }

    public function getRotationAsString(): string
    {
        if ($this->rotation == static::ROT_MONTHLY) {
            return "monatlich";
        } elseif ($this->rotation == static::ROT_QUATERLY) {
            return "vierteljährlich";
        }

        return "not found";
    }

    public function setRotation(int $rotation): self
    {
        $this->rotation = $rotation;

        return $this;
    }

    /**
     * @return null|DateTimeInterface
     */
    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getLastAccomplished(): ?\DateTimeInterface
    {
        return $this->lastAccomplished;
    }

    public function setLastAccomplished(?\DateTimeInterface $lastAccomplished): self
    {
        $this->lastAccomplished = $lastAccomplished;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (! $this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }
}
