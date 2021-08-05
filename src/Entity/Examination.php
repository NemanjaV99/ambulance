<?php

namespace App\Entity;

use App\Repository\ExaminationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use DateTime;

/**
 * @ORM\Entity(repositoryClass=ExaminationRepository::class)
 */
class Examination
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="examinations")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid(groups={"create_examination"})
     */
    private $patient;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type(type="\DateTimeInterface",groups={"create_examination"})
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=Doctor::class, inversedBy="examinations")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(groups={"create_examination"})
     */
    private $doctor;

    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $diagnosis;

    /**
     * @ORM\Column(type="boolean")
     */
    private $performed = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;

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

    public function getDoctor(): ?Doctor
    {
        return $this->doctor;
    }

    public function setDoctor(?Doctor $doctor): self
    {
        $this->doctor = $doctor;

        return $this;
    }

    public function getDiagnosis(): ?string
    {
        return $this->diagnosis;
    }

    public function setDiagnosis(?string $diagnosis): self
    {
        $this->diagnosis = $diagnosis;

        return $this;
    }

    public function getPerformed(): ?bool
    {
        return $this->performed;
    }

    public function setPerformed(bool $performed): self
    {
        $this->performed = $performed;

        return $this;
    }

    /**
     *  * @Assert\Callback(groups={"create_examination"})
     *  Callback for validating that a date is not set to a past date
     */
    public function validatePastDate(ExecutionContextInterface $context)
    {
        // This should contain the user input date
        $passedDate = $this->getDate();
        $currentDate = new DateTime();

        if ($passedDate < $currentDate) {

            $context->buildViolation("Date can't be set to a past date.")
                ->atPath('date')
                ->addViolation();
        }

    }
}
