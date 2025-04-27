<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\CandidatureRepository;

#[ORM\Entity(repositoryClass: CandidatureRepository::class)]
#[ORM\Table(name: 'candidature')]
class Candidature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_candidature = null;

    #[ORM\ManyToOne(targetEntity: Offre::class, inversedBy: 'candidatures')]
    #[ORM\JoinColumn(name: 'id_offre', referencedColumnName: 'id_offre')]
    private ?Offre $offre = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'candidatures')]
    #[ORM\JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id')]
    private ?User $user = null;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $date_candidature = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $statut = null;

    #[ORM\Column(type: 'blob', nullable: false)]
    private $cv_file = null;

    #[ORM\Column(type: 'text', nullable: false)]
    #[Assert\NotBlank(message: "La lettre de motivation ne peut pas être vide")]
    #[Assert\Length(
        min: 20,
        max: 2000,
        minMessage: "La lettre de motivation doit faire au moins {{ limit }} caractères",
        maxMessage: "La lettre de motivation ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $lettre_motivation = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $score = null;

    public function getId_candidature(): ?int
    {
        return $this->id_candidature;
    }

    public function setId_candidature(int $id_candidature): self
    {
        $this->id_candidature = $id_candidature;
        return $this;
    }

    public function getOffre(): ?Offre
    {
        return $this->offre;
    }

    public function setOffre(?Offre $offre): self
    {
        $this->offre = $offre;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getDate_candidature(): ?\DateTimeInterface
    {
        return $this->date_candidature;
    }

    public function setDate_candidature(\DateTimeInterface $date_candidature): self
    {
        $this->date_candidature = $date_candidature;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getCv_file(): ?string
    {
        return $this->cv_file;
    }

    public function setCv_file($cv_file): self
    {
        $this->cv_file = $cv_file;
        return $this;
    }

    public function getLettre_motivation(): ?string
    {
        return $this->lettre_motivation;
    }

    public function setLettre_motivation(?string $lettre_motivation): self
    {
        $this->lettre_motivation = $lettre_motivation;
        return $this;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(?float $score): self
    {
        $this->score = $score;
        return $this;
    }

    public function getIdCandidature(): ?int
    {
        return $this->id_candidature;
    }

    public function getDateCandidature(): ?\DateTimeInterface
    {
        return $this->date_candidature;
    }

    public function setDateCandidature(\DateTimeInterface $date_candidature): static
    {
        $this->date_candidature = $date_candidature;
        return $this;
    }

    public function getCvFile()
    {
        return $this->cv_file;
    }

    public function setCvFile($cv_file): static
    {
        $this->cv_file = $cv_file;
        return $this;
    }

    public function getLettreMotivation(): ?string
    {
        return $this->lettre_motivation;
    }

    public function setLettreMotivation(?string $lettre_motivation): static
    {
        $this->lettre_motivation = $lettre_motivation;
        return $this;
    }
}