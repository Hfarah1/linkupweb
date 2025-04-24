<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\RatingRepository;

#[ORM\Entity(repositoryClass: RatingRepository::class)]
#[ORM\Table(name: 'rating')]
class Rating
{
    #[ORM\Id]
#[ORM\GeneratedValue]
#[ORM\Column(name: "idRating", type: 'integer')]
private ?int $idRating = null;

    public function getIdRating(): ?int
    {
        return $this->idRating;
    }

    public function setIdRating(int $idRating): self
    {
        $this->idRating = $idRating;
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

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $commentaire = null;

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'ratings')]
    #[ORM\JoinColumn(name: 'fkidEvent', referencedColumnName: 'id_event')]
    private ?Event $event = null;

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;
        return $this;
    }

}
