<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\DateTimeTzType;


#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateReservation = null;

    #[ORM\Column(length: 255)]
    private ?string $stationDepart = null;

    #[ORM\Column(length: 255)]
    private ?string $stationFin = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?User $idUser = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateReservation(): ?\DateTimeInterface
    {
        return $this->dateReservation;
    }

    public function setDateReservation(\DateTimeInterface $dateReservation): static
    {
        $this->dateReservation = $dateReservation;

        return $this;
    }

    public function getStationDepart(): ?string
    {
        return $this->stationDepart;
    }

    public function setStationDepart(string $stationDepart): static
    {
        $this->stationDepart = $stationDepart;

        return $this;
    }

    public function getStationFin(): ?string
    {
        return $this->stationFin;
    }

    public function setStationFin(string $stationFin): static
    {
        $this->stationFin = $stationFin;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }
}
