<?php

namespace Prolyfix\BankingBundle\Entity;

use App\Entity\Commentable;
use Prolyfix\BankingBundle\Repository\EntryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntryRepository::class)]
class Entry extends Commentable
{

    #[ORM\ManyToOne(inversedBy: 'entries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $bank = null;

    #[ORM\Column]
    private ?\DateTime $date = null;

    #[ORM\Column(length: 255)]
    private ?string $counterpart = null;

    #[ORM\Column(length: 511)]
    private ?string $title = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\ManyToOne(inversedBy: 'entries')]
    private ?AccountType $type = null;

    public function getBank(): ?Account
    {
        return $this->bank;
    }

    public function setBank(?Account $bank): static
    {
        $this->bank = $bank;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getCounterpart(): ?string
    {
        return $this->counterpart;
    }

    public function setCounterpart(string $counterpart): static
    {
        $this->counterpart = $counterpart;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getType(): ?AccountType
    {
        return $this->type;
    }

    public function setType(?AccountType $type): static
    {
        $this->type = $type;

        return $this;
    }
}
