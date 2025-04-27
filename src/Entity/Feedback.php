<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\FeedbackRepository;

#[ORM\Entity(repositoryClass: FeedbackRepository::class)]
#[ORM\Table(name: 'feedbacks')]
class Feedback
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_feedback = null;

    public function getId_feedback(): ?int
    {
        return $this->id_feedback;
    }

    public function setId_feedback(int $id_feedback): self
    {
        $this->id_feedback = $id_feedback;
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

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $id_utilisateur = null;

    public function getId_utilisateur(): ?int
    {
        return $this->id_utilisateur;
    }

    public function setId_utilisateur(int $id_utilisateur): self
    {
        $this->id_utilisateur = $id_utilisateur;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: false)]
    private ?string $commentaire = null;

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $note = null;

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(?int $note): self
    {
        $this->note = $note;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $reply_to = null;

    public function getReply_to(): ?int
    {
        return $this->reply_to;
    }

    public function setReply_to(?int $reply_to): self
    {
        $this->reply_to = $reply_to;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $responded_by = null;

    public function getResponded_by(): ?int
    {
        return $this->responded_by;
    }

    public function setResponded_by(?int $responded_by): self
    {
        $this->responded_by = $responded_by;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $date_commentaire = null;

    public function getDate_commentaire(): ?\DateTimeInterface
    {
        return $this->date_commentaire;
    }

    public function setDate_commentaire(\DateTimeInterface $date_commentaire): self
    {
        $this->date_commentaire = $date_commentaire;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $date_reponse = null;

    public function getDate_reponse(): ?\DateTimeInterface
    {
        return $this->date_reponse;
    }

    public function setDate_reponse(?\DateTimeInterface $date_reponse): self
    {
        $this->date_reponse = $date_reponse;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $post = null;

    public function getPost(): ?string
    {
        return $this->post;
    }

    public function setPost(?string $post): self
    {
        $this->post = $post;
        return $this;
    }

    public function getIdFeedback(): ?int
    {
        return $this->id_feedback;
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

    public function getIdUtilisateur(): ?int
    {
        return $this->id_utilisateur;
    }

    public function setIdUtilisateur(int $id_utilisateur): static
    {
        $this->id_utilisateur = $id_utilisateur;

        return $this;
    }

    public function getReplyTo(): ?int
    {
        return $this->reply_to;
    }

    public function setReplyTo(?int $reply_to): static
    {
        $this->reply_to = $reply_to;

        return $this;
    }

    public function getRespondedBy(): ?int
    {
        return $this->responded_by;
    }

    public function setRespondedBy(?int $responded_by): static
    {
        $this->responded_by = $responded_by;

        return $this;
    }

    public function getDateCommentaire(): ?\DateTimeInterface
    {
        return $this->date_commentaire;
    }

    public function setDateCommentaire(\DateTimeInterface $date_commentaire): static
    {
        $this->date_commentaire = $date_commentaire;

        return $this;
    }

    public function getDateReponse(): ?\DateTimeInterface
    {
        return $this->date_reponse;
    }

    public function setDateReponse(?\DateTimeInterface $date_reponse): static
    {
        $this->date_reponse = $date_reponse;

        return $this;
    }

    /**
     * Get the associated utilisateur
     */
    private ?Utilisateur $utilisateur = null;
    
    /**
     * Get the associated utilisateur
     */
    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }
    
    /**
     * Set the associated utilisateur
     */
    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }
}
