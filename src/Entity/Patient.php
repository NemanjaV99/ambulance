<?php

namespace App\Entity;

use App\Repository\PatientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validation\Constraints as CustomAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=PatientRepository::class)
 * @UniqueEntity("jmbg")
 */
class Patient
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="first_name", type="string", length=100)
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 3,
     *      max = 100
     * )
     * @Assert\Type(type="alpha")
     */
    private $firstName;

    /**
     * @ORM\Column(name="last_name", type="string", length=100)
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 3,
     *      max = 100
     * )
     * @Assert\Type(type="alpha")
     */
    private $lastName;

    /**
     * @ORM\ManyToOne(targetEntity=Location::class, inversedBy="patients")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid
     */
    private $location;

    /**
     * @ORM\Column(name="jmbg", type="string", length=13, unique=true)
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 13,
     *      max = 13,
     *      exactMessage = "The JMBG number should have exactly 13 numeric characters."
     * )
     * @Assert\Type(type="numeric", message="This value should be a number.")
     */
    private $jmbg;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     * @Assert\Length(
     *      max = 1000
     * )
     * 
     */
    private $note;

    /**
     * @ORM\OneToMany(targetEntity=Examination::class, mappedBy="patient", orphanRemoval=true)
     */
    private $examinations;

    public function __construct()
    {
        $this->examinations = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getJMBG(): ?string
    {
        return $this->jmbg;
    }

    public function setJMBG(string $jmbg): self
    {
        $this->jmbg = $jmbg;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        // Trim to prevent setting empty string as value, since note is not required - can be null
        $this->note = trim($note);

        return $this;
    }

    /**
     * @return Collection|Examination[]
     */
    public function getExaminations(): Collection
    {
        return $this->examinations;
    }

    public function addExamination(Examination $examination): self
    {
        if (!$this->examinations->contains($examination)) {
            $this->examinations[] = $examination;
            $examination->setPatient($this);
        }

        return $this;
    }

    public function removeExamination(Examination $examination): self
    {
        if ($this->examinations->removeElement($examination)) {
            // set the owning side to null (unless already changed)
            if ($examination->getPatient() === $this) {
                $examination->setPatient(null);
            }
        }

        return $this;
    }
}
