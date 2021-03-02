<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Distances
 *
 * @ORM\Table(name="distances")
 * @ORM\Entity
 */
class Distances
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
     * @ORM\Column(name="airport_1", type="string", length=255, nullable=false)
     */
    private $airport1;

    /**
     * @var string
     *
     * @ORM\Column(name="airport_2", type="string", length=255, nullable=false)
     */
    private $airport2;

    /**
     * @var int
     *
     * @ORM\Column(name="kilometers", type="integer", nullable=false)
     */
    private $kilometers;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getAirport1(): ?string
    {
        return $this->airport1;
    }

    public function setAirport1(string $airport1): self
    {
        $this->airport1 = $airport1;

        return $this;
    }

    public function getAirport2(): ?string
    {
        return $this->airport2;
    }

    public function setAirport2(string $airport2): self
    {
        $this->airport2 = $airport2;

        return $this;
    }

    public function getKilometers(): ?int
    {
        return $this->kilometers;
    }

    public function setKilometers(int $kilometers): self
    {
        $this->kilometers = $kilometers;

        return $this;
    }


}
