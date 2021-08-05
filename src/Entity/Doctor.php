<?php

namespace App\Entity;

use App\Repository\DoctorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DoctorRepository::class)
 */
class Doctor
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(groups={"doctor_create", "doctor_update"})
     * @Assert\Length(
     *      min = 3,
     *      max = 100,
     *      groups={"doctor_create", "doctor_update"}
     * )
     * @Assert\Type(type="alpha", groups={"doctor_create", "doctor_update"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(groups={"doctor_create", "doctor_update"})
     * @Assert\Length(
     *      min = 3,
     *      max = 100,
     *      groups={"doctor_create", "doctor_update"}
     * )
     * @Assert\Type(type="alpha", groups={"doctor_create", "doctor_update"})
     */
    private $lastName;

    /**
     * @ORM\ManyToOne(targetEntity=TypeDoctor::class, inversedBy="doctors")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid(groups={"doctor_create", "doctor_update"})
     */
    private $type;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid(groups={"doctor_create", "doctor_update"})
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Examination::class, mappedBy="doctor", orphanRemoval=true)
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

    public function getType(): ?TypeDoctor
    {
        return $this->type;
    }

    public function setType(?TypeDoctor $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

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
            $examination->setDoctor($this);
        }

        return $this;
    }

    public function removeExamination(Examination $examination): self
    {
        if ($this->examinations->removeElement($examination)) {
            // set the owning side to null (unless already changed)
            if ($examination->getDoctor() === $this) {
                $examination->setDoctor(null);
            }
        }

        return $this;
    }
}
