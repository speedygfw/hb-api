<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use App\Repository\BookingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\MongoDbOdm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter as FilterDateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotNull;
use ApiPlatform\Core\Bridge\Elasticsearch\DataProvider\Filter\OrderFilter;

#[ApiResource(
        //collectionOperations: ['get' => ['normalization_context' => ['groups' => 'bookings:list'] ]],
    itemOperations: [
        'get' => ["access_control"=>"is_granted('ROLE_USER') and object.getUser().getId() == user.getId()"],
        'patch' => ["access_control"=>"is_granted('ROLE_USER') and object.getUser().getId() == user.getId()"],
        #'post' => ["access_control"=>"is_granted('ROLE_USER') and object.getUser().getId() == user.getId()"],
        'put' => ["access_control"=>"is_granted('ROLE_USER') and object.getUser().getId() == user.getId()"],
        'delete' => ["access_control"=>"is_granted('ROLE_USER') and object.getUser().getId() == user.getId()"]
    
    ],
    paginationEnabled: false,
    normalizationContext: ['groups' => 'read', "datetime_format"=>"Y-m-d",],
    denormalizationContext: ['groups' => 'write', "datetime_format" => "Y-m-d|"]
), ApiFilter(
    SearchFilter::class,
    properties: [
        'name' => SearchFilter::STRATEGY_PARTIAL,
        'bookingDate' => SearchFilter::STRATEGY_PARTIAL,
        'category.name' => SearchFilter::STRATEGY_PARTIAL
    ],
    
), ApiFilter(
    FilterDateFilter::class,
    properties:[
        'bookingDate'
    ]
)]
#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    public const INCOME = 0;

    public const OUTCOME = 1;

    public const ROT_INCOME = 3;

    public const ROT_OUTCOME = 4;

    public const ROT_ONCE = "once";

    public const ROT_MONTHLY = "monthly";

    public const ROT_QUATERL = "quaterly";

    public const ROT_YEARLY = 'yearly';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read'])]
    private $id;

    #[ORM\Column(type: 'smallint')]
    #[Groups(['read', 'write'])]
    private $type;

    #[ORM\Column(type: 'float')]
    #[Groups(['read', 'write'])]
    private $amount;

    #[NotBlank]
    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read', 'write'])]
    private $name;

    //* @var \DateTimeImmutable *//
    #[ORM\Column(type: 'date')]
    #[Groups(['read', 'write', 'date'])]
    private $bookingDate = null;

    #[ApiProperty(readableLink: true, writableLink: true)]
    #[Groups(['read', 'write'])]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[Groups(['read', 'write'])]
    #[ORM\ManyToMany(
        targetEntity: Category::class,
        inversedBy: 'bookings',
        cascade: ['persist']
    )]
    private $category;

    #[ORM\ManyToOne(targetEntity: Contract::class, inversedBy: 'booking')]
    private $contract;

    public function __construct()
    {
        $this->category = new ArrayCollection();
        $this->bookingDate = new \DateTime();
        $this->type = self::OUTCOME;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
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

    public function getBookingDate(): ?\DateTimeInterface
    {
        return $this->bookingDate;
    }

    public function setBookingDate(\DateTimeInterface $bookingDate): self
    {
        $this->bookingDate = $bookingDate;

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

    /**
     * @return Collection<int, Category>
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Category $category): self
    {
        if (! $this->category->contains($category)) {
            $this->category[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->category->removeElement($category);

        return $this;
    }

    public function getContract(): ?Contract
    {
        return $this->contract;
    }

    public function setContract(?Contract $contract): self
    {
        $this->contract = $contract;

        return $this;
    }
}
