<?php

namespace App\Entity;

use App\Repository\RentalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RentalRepository::class)]
class Rental
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $rentaldate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $returndate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $actuelreturndate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $totalcost = null;

    #[ORM\Column]
    private ?bool $islate = null;

    #[ORM\ManyToOne(inversedBy: 'rental')]
    private ?Car $car = null;

    #[ORM\ManyToOne(inversedBy: 'rentals')]
    private ?Customer $customer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRentaldate(): ?\DateTimeInterface
    {
        return $this->rentaldate;
    }

    public function setRentaldate(\DateTimeInterface $rentaldate): static
    {
        $this->rentaldate = $rentaldate;

        return $this;
    }

    public function getReturndate(): ?\DateTimeInterface
    {
        return $this->returndate;
    }

    public function setReturndate(\DateTimeInterface $returndate): static
    {
        $this->returndate = $returndate;

        return $this;
    }

    public function getActuelreturndate(): ?\DateTimeInterface
    {
        return $this->actuelreturndate;
    }

    public function setActuelreturndate(\DateTimeInterface $actuelreturndate): static
    {
        $this->actuelreturndate = $actuelreturndate;

        return $this;
    }

    public function getTotalcost(): ?string
    {
        return $this->totalcost;
    }

    public function setTotalcost(string $totalcost): static
    {
        $this->totalcost = $totalcost;

        return $this;
    }

    public function islate(): ?bool
    {
        return $this->islate;
    }

    public function setLate(bool $islate): static
    {
        $this->islate = $islate;

        return $this;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): static
    {
        $this->car = $car;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }
}
