<?php

namespace App\Entity;

use App\Repository\ChoreAssignmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ChoreAssignmentRepository::class)]
class ChoreAssignment
{
    public const DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    public const STATUS_PENDING = 'pending';
    public const STATUS_DONE = 'done';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Household $household = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Task $task = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $assignedTo = null;

    #[ORM\Column(length: 10)]
    #[Assert\Choice(choices: self::DAYS)]
    private ?string $day = null;

    #[ORM\Column]
    #[Assert\Range(min: 1, max: 53)]
    private ?int $weekNumber = null;

    #[ORM\Column]
    private ?int $year = null;

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_PENDING;

    public function getId(): ?int { return $this->id; }
    public function getHousehold(): ?Household { return $this->household; }
    public function setHousehold(?Household $household): static { $this->household = $household; return $this; }
    public function getTask(): ?Task { return $this->task; }
    public function setTask(?Task $task): static { $this->task = $task; return $this; }
    public function getAssignedTo(): ?User { return $this->assignedTo; }
    public function setAssignedTo(?User $user): static { $this->assignedTo = $user; return $this; }
    public function getDay(): ?string { return $this->day; }
    public function setDay(string $day): static { $this->day = $day; return $this; }
    public function getWeekNumber(): ?int { return $this->weekNumber; }
    public function setWeekNumber(int $weekNumber): static { $this->weekNumber = $weekNumber; return $this; }
    public function getYear(): ?int { return $this->year; }
    public function setYear(int $year): static { $this->year = $year; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }
}
