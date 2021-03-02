<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\FlightsRepository;

/**
 * Flights
 *
 * @ORM\Table(name="flights", indexes={@ORM\Index(name="flights_departure_airport_id_foreign", columns={"departure_airport_id"}), @ORM\Index(name="flights_arrival_airport_id_foreign", columns={"arrival_airport_id"})})
 * @ORM\Entity(repositoryClass=FlightsRepository::class)
 */
class Flights
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private $code;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="departure_date", type="datetime", nullable=false)
     */
    private $departureDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="arrival_date", type="datetime", nullable=false)
     */
    private $arrivalDate;

    /**
     * @var int
     *
     * @ORM\Column(name="airplane_id", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $airplaneId;

    /**
     * @var int
     *
     * @ORM\Column(name="duration", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $duration;

    /**
     * @var string
     *
     * @ORM\Column(name="base_price", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $basePrice;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=0, nullable=false, options={"default"="scheduled"})
     */
    private $status = 'scheduled';

    /**
     * @var \Airports
     *
     * @ORM\ManyToOne(targetEntity="Airports")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="arrival_airport_id", referencedColumnName="id")
     * })
     */
    private $arrivalAirport;

    /**
     * @var \Airports
     *
     * @ORM\ManyToOne(targetEntity="Airports")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="departure_airport_id", referencedColumnName="id")
     * })
     */
    private $departureAirport;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getDepartureDate(): ?\DateTimeInterface
    {
        return $this->departureDate;
    }

    public function setDepartureDate(\DateTimeInterface $departureDate): self
    {
        $this->departureDate = $departureDate;

        return $this;
    }

    public function getArrivalDate(): ?\DateTimeInterface
    {
        return $this->arrivalDate;
    }

    public function setArrivalDate(\DateTimeInterface $arrivalDate): self
    {
        $this->arrivalDate = $arrivalDate;

        return $this;
    }

    public function getAirplaneId(): ?string
    {
        return $this->airplaneId;
    }

    public function setAirplaneId(string $airplaneId): self
    {
        $this->airplaneId = $airplaneId;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getBasePrice(): ?string
    {
        return $this->basePrice;
    }

    public function setBasePrice(string $basePrice): self
    {
        $this->basePrice = $basePrice;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getArrivalAirport(): ?Airports
    {
        return $this->arrivalAirport;
    }

    public function setArrivalAirport(?Airports $arrivalAirport): self
    {
        $this->arrivalAirport = $arrivalAirport;

        return $this;
    }

    public function getDepartureAirport(): ?Airports
    {
        return $this->departureAirport;
    }

    public function setDepartureAirport(?Airports $departureAirport): self
    {
        $this->departureAirport = $departureAirport;

        return $this;
    }


}
