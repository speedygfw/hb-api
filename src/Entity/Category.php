<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    //collectionOperations: ['get' => ['normalization_context' => ['groups' => 'bookings:list'] ]],
    // itemOperations: ['get' => ['normalization_context' => ['groups' => 'booking:item']]],
    paginationEnabled: false,
    normalizationContext: ['groups' => 'read', "datetime_format"=>"Y-m-d",],
    denormalizationContext: ['groups' => 'write', "datetime_format" => "Y-m-d|"]
)]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    // #[Groups(['read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\ManyToMany(targetEntity: Booking::class, mappedBy: 'category')]
    private $bookings;

    #[ORM\ManyToMany(targetEntity: Contract::class, mappedBy: 'categories')]
    private $contracts;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->contracts = new ArrayCollection();
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

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (! $this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->addCategory($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            $booking->removeCategory($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return Collection<int, Contract>
     */
    public function getContracts(): Collection
    {
        return $this->contracts;
    }

    public function addContract(Contract $contract): self
    {
        if (! $this->contracts->contains($contract)) {
            $this->contracts[] = $contract;
            $contract->addCategory($this);
        }

        return $this;
    }

    public function removeContract(Contract $contract): self
    {
        if ($this->contracts->removeElement($contract)) {
            $contract->removeCategory($this);
        }

        return $this;
    }
}
