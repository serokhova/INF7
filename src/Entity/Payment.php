<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_LATE = 'late';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $tenant = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Household $household = null;

    #[ORM\Column(type: 'decimal', precision: 8, scale: 2)]
    #[Assert\Positive]
    private ?string $rentAmount = null;

    #[ORM\Column(type: 'decimal', precision: 8, scale: 2)]
    #[Assert\PositiveOrZero]
    private ?string $chargesAmount = null;

    #[ORM\Column(length: 7)]
    #[Assert\Regex('/^\d{4}-\d{2}$/', message: 'Format attendu : YYYY-MM')]
    private ?string $period = null;

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_PENDING;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $paidAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getTenant(): ?User { return $this->tenant; }
    public function setTenant(?User $tenant): static { $this->tenant = $tenant; return $this; }
    public function getHousehold(): ?Household { return $this->household; }
    public function setHousehold(?Household $household): static { $this->household = $household; return $this; }
    public function getRentAmount(): ?string { return $this->rentAmount; }
    public function setRentAmount(string $rentAmount): static { $this->rentAmount = $rentAmount; return $this; }
    public function getChargesAmount(): ?string { return $this->chargesAmount; }
    public function setChargesAmount(string $chargesAmount): static { $this->chargesAmount = $chargesAmount; return $this; }
    public function getPeriod(): ?string { return $this->period; }
    public function setPeriod(string $period): static { $this->period = $period; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }
    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function getPaidAt(): ?\DateTimeImmutable { return $this->paidAt; }
    public function setPaidAt(?\DateTimeImmutable $paidAt): static { $this->paidAt = $paidAt; return $this; }

    public function getTotalAmount(): string
    {
        return number_format(((float) $this->rentAmount) + ((float) $this->chargesAmount), 2, '.', '');
    }
}
