<?php

namespace App\Entity;

use App\Repository\ExpenseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ExpenseRepository::class)]
class Expense
{
    public const CATEGORIES = ['water', 'electricity', 'internet', 'taxes', 'other'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Household $household = null;

    #[ORM\Column(length: 30)]
    #[Assert\Choice(choices: self::CATEGORIES)]
    private ?string $category = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    private ?string $label = null;

    #[ORM\Column(type: 'decimal', precision: 8, scale: 2)]
    #[Assert\Positive]
    private ?string $amount = null;

    #[ORM\Column(length: 7)]
    #[Assert\Regex('/^\d{4}-\d{2}$/', message: 'Format attendu : YYYY-MM')]
    private ?string $period = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getHousehold(): ?Household { return $this->household; }
    public function setHousehold(?Household $household): static { $this->household = $household; return $this; }
    public function getCategory(): ?string { return $this->category; }
    public function setCategory(string $category): static { $this->category = $category; return $this; }
    public function getLabel(): ?string { return $this->label; }
    public function setLabel(string $label): static { $this->label = $label; return $this; }
    public function getAmount(): ?string { return $this->amount; }
    public function setAmount(string $amount): static { $this->amount = $amount; return $this; }
    public function getPeriod(): ?string { return $this->period; }
    public function setPeriod(string $period): static { $this->period = $period; return $this; }
    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
}
