<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Airplanes
 *
 * @ORM\Table(name="airplanes", indexes={@ORM\Index(name="airplanes_airline_id_foreign", columns={"airline_id"})})
 * @ORM\Entity
 */
class Airplanes
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
     * @ORM\Column(name="model", type="string", length=255, nullable=false)
     */
    private $model;

    /**
     * @var int
     *
     * @ORM\Column(name="economy_class_seats", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $economyClassSeats;

    /**
     * @var int
     *
     * @ORM\Column(name="first_class_seats", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $firstClassSeats;

    /**
     * @var \Airlines
     *
     * @ORM\ManyToOne(targetEntity="Airlines")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="airline_id", referencedColumnName="id")
     * })
     */
    private $airline;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getEconomyClassSeats(): ?int
    {
        return $this->economyClassSeats;
    }

    public function setEconomyClassSeats(int $economyClassSeats): self
    {
        $this->economyClassSeats = $economyClassSeats;

        return $this;
    }

    public function getFirstClassSeats(): ?int
    {
        return $this->firstClassSeats;
    }

    public function setFirstClassSeats(int $firstClassSeats): self
    {
        $this->firstClassSeats = $firstClassSeats;

        return $this;
    }

    public function getAirline(): ?Airlines
    {
        return $this->airline;
    }

    public function setAirline(?Airlines $airline): self
    {
        $this->airline = $airline;

        return $this;
    }


}
