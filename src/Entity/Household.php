<?php

namespace App\Entity;

use App\Repository\HouseholdRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HouseholdRepository::class)]
class Household
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\OneToMany(mappedBy: 'household', targetEntity: User::class)]
    private Collection $tenants;

    #[ORM\Column(type: 'decimal', precision: 8, scale: 2, options: ['default' => 0])]
    private string $monthlyCharges = '0.00';

    public function __construct()
    {
        $this->tenants = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }
    public function getAddress(): ?string { return $this->address; }
    public function setAddress(string $address): static { $this->address = $address; return $this; }
    public function getOwner(): ?User { return $this->owner; }
    public function setOwner(?User $owner): static { $this->owner = $owner; return $this; }
    public function getTenants(): Collection { return $this->tenants; }
    public function getMonthlyCharges(): string { return $this->monthlyCharges; }
    public function setMonthlyCharges(string $monthlyCharges): static { $this->monthlyCharges = $monthlyCharges; return $this; }

    public function addTenant(User $tenant): static
    {
        if (!$this->tenants->contains($tenant)) {
            $this->tenants->add($tenant);
            $tenant->setHousehold($this);
        }

        return $this;
    }

    public function removeTenant(User $tenant): static
    {
        if ($this->tenants->removeElement($tenant)) {
            if ($tenant->getHousehold() === $this) {
                $tenant->setHousehold(null);
            }
        }

        return $this;
    }
}
