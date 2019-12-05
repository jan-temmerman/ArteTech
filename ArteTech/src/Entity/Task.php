<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $employee;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="float")
     */
    private $hoursWorked;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PauseLength")
     * @ORM\JoinColumn(nullable=false)
     */
    private $pauseLength;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Period", inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $period;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $materialsUsed;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmployee(): ?User
    {
        return $this->employee;
    }

    public function setEmployee(?User $employee): self
    {
        $this->employee = $employee;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getHoursWorked(): ?float
    {
        return $this->hoursWorked;
    }

    public function setHoursWorked(float $hoursWorked): self
    {
        $this->hoursWorked = $hoursWorked;

        return $this;
    }

    public function getPauseLength(): ?PauseLength
    {
        return $this->pauseLength;
    }

    public function setPauseLength(?PauseLength $pauseLength): self
    {
        $this->pauseLength = $pauseLength;

        return $this;
    }

    public function getPeriod(): ?Period
    {
        return $this->period;
    }

    public function setPeriod(?Period $period): self
    {
        $this->period = $period;

        return $this;
    }

    public function getMaterialsUsed(): ?string
    {
        return $this->materialsUsed;
    }

    public function setMaterialsUsed(string $materialsUsed): self
    {
        $this->materialsUsed = $materialsUsed;

        return $this;
    }
}
