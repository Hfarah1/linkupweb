<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\NewTableNamematchEntityRepository;

#[ORM\Entity(repositoryClass: NewTableNamematchEntityRepository::class)]
#[ORM\Table(name: 'new_table_namematch_entity')]
class NewTableNamematchEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_match = null;

    public function getId_match(): ?int
    {
        return $this->id_match;
    }

    public function setId_match(int $id_match): self
    {
        $this->id_match = $id_match;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $id_utilisateur1 = null;

    public function getId_utilisateur1(): ?int
    {
        return $this->id_utilisateur1;
    }

    public function setId_utilisateur1(int $id_utilisateur1): self
    {
        $this->id_utilisateur1 = $id_utilisateur1;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $id_utilisateur2 = null;

    public function getId_utilisateur2(): ?int
    {
        return $this->id_utilisateur2;
    }

    public function setId_utilisateur2(int $id_utilisateur2): self
    {
        $this->id_utilisateur2 = $id_utilisateur2;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $id_rencontre = null;

    public function getId_rencontre(): ?int
    {
        return $this->id_rencontre;
    }

    public function setId_rencontre(int $id_rencontre): self
    {
        $this->id_rencontre = $id_rencontre;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $score_match = null;

    public function getScore_match(): ?int
    {
        return $this->score_match;
    }

    public function setScore_match(?int $score_match): self
    {
        $this->score_match = $score_match;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $date_match = null;

    public function getDate_match(): ?\DateTimeInterface
    {
        return $this->date_match;
    }

    public function setDate_match(\DateTimeInterface $date_match): self
    {
        $this->date_match = $date_match;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $date_expiration = null;

    public function getDate_expiration(): ?\DateTimeInterface
    {
        return $this->date_expiration;
    }

    public function setDate_expiration(?\DateTimeInterface $date_expiration): self
    {
        $this->date_expiration = $date_expiration;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $statut = null;

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getIdMatch(): ?int
    {
        return $this->id_match;
    }

    public function getIdUtilisateur1(): ?int
    {
        return $this->id_utilisateur1;
    }

    public function setIdUtilisateur1(int $id_utilisateur1): static
    {
        $this->id_utilisateur1 = $id_utilisateur1;

        return $this;
    }

    public function getIdUtilisateur2(): ?int
    {
        return $this->id_utilisateur2;
    }

    public function setIdUtilisateur2(int $id_utilisateur2): static
    {
        $this->id_utilisateur2 = $id_utilisateur2;

        return $this;
    }

    public function getIdRencontre(): ?int
    {
        return $this->id_rencontre;
    }

    public function setIdRencontre(int $id_rencontre): static
    {
        $this->id_rencontre = $id_rencontre;

        return $this;
    }

    public function getScoreMatch(): ?int
    {
        return $this->score_match;
    }

    public function setScoreMatch(?int $score_match): static
    {
        $this->score_match = $score_match;

        return $this;
    }

    public function getDateMatch(): ?\DateTimeInterface
    {
        return $this->date_match;
    }

    public function setDateMatch(\DateTimeInterface $date_match): static
    {
        $this->date_match = $date_match;

        return $this;
    }

    public function getDateExpiration(): ?\DateTimeInterface
    {
        return $this->date_expiration;
    }

    public function setDateExpiration(?\DateTimeInterface $date_expiration): static
    {
        $this->date_expiration = $date_expiration;

        return $this;
    }

}
