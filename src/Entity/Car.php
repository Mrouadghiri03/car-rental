<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarRepository::class)]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $brand = null;

    #[ORM\Column(length: 50)]
    private ?string $model = null;

    #[ORM\Column]
    private ?int $year = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $priceperday = null;

    #[ORM\ManyToOne(inversedBy: 'cars')]
    private ?Category $category = null;

    /**
     * @var Collection<int, Rental>
     */
    #[ORM\OneToMany(targetEntity: Rental::class, mappedBy: 'car')]
    private Collection $rental;

    public function __construct()
    {
        $this->rental = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getPriceperday(): ?string
    {
        return $this->priceperday;
    }

    public function setPriceperday(string $priceperday): static
    {
        $this->priceperday = $priceperday;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Rental>
     */
    public function getRental(): Collection
    {
        return $this->rental;
    }

    public function addRental(Rental $rental): static
    {
        if (!$this->rental->contains($rental)) {
            $this->rental->add($rental);
            $rental->setCar($this);
        }

        return $this;
    }

    public function removeRental(Rental $rental): static
    {
        if ($this->rental->removeElement($rental)) {
            // set the owning side to null (unless already changed)
            if ($rental->getCar() === $this) {
                $rental->setCar(null);
            }
        }

        return $this;
    }
}
