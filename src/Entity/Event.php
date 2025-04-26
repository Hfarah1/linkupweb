<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Repository\EventRepository;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\Table(name: 'events')]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image_path = null;

    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'events')]
    #[ORM\JoinColumn(name: 'categorie_id', referencedColumnName: 'id_categorie', nullable: false)]
    private ?Categorie $categorie = null;

    #[ORM\OneToMany(targetEntity: Rating::class, mappedBy: 'event')]
    private Collection $ratings;

    public function __construct()
    {
        $this->ratings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;
        return $this;
    }

    // Alias methods for backwards compatibility
    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->getDateDebut();
    }

    public function setStartDate(\DateTimeInterface $date_debut): static
    {
        return $this->setDateDebut($date_debut);
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): static
    {
        $this->date_fin = $date_fin;
        return $this;
    }

    // Alias methods for backwards compatibility
    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->getDateFin();
    }

    public function setEndDate(\DateTimeInterface $date_fin): static
    {
        return $this->setDateFin($date_fin);
    }

    public function getImagePath(): ?string
    {
        return $this->image_path;
    }

    public function setImagePath(?string $image_path): static
    {
        $this->image_path = $image_path;
        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;
        return $this;
    }

    /**
     * @return Collection<int, Rating>
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): static
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings->add($rating);
            $rating->setEvent($this);
        }
        return $this;
    }

    public function removeRating(Rating $rating): static
    {
        if ($this->ratings->removeElement($rating)) {
            if ($rating->getEvent() === $this) {
                $rating->setEvent(null);
            }
        }
        return $this;
    }

    // Nouveaux champs pour les sessions de coaching virtuelles
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $type = null; // 'in_person' ou 'virtual'

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $channel = null; // Canal Agora pour les sessions virtuelles

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $app_id = null; // ID d'application Agora

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $token = null; // Token Agora

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $duree = null; // Durée de la session

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $acces = null; // Accès à la session

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $code = null; // Code d'accès à la session

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lieu = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $places = null;

    public function getIdEvent(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getChannel(): ?string
    {
        return $this->channel;
    }

    public function setChannel(?string $channel): self
    {
        $this->channel = $channel;
        return $this;
    }

    public function getAppId(): ?string
    {
        return $this->app_id;
    }

    public function setAppId(?string $app_id): self
    {
        $this->app_id = $app_id;
        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;
        return $this;
    }

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(?string $duree): self
    {
        $this->duree = $duree;
        return $this;
    }

    public function getAcces(): ?string
    {
        return $this->acces;
    }

    public function setAcces(?string $acces): self
    {
        $this->acces = $acces;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(?string $lieu): static
    {
        $this->lieu = $lieu;
        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): static
    {
        $this->ville = $ville;
        return $this;
    }

    public function getPlaces(): ?int
    {
        return $this->places;
    }

    public function setPlaces(?int $places): static
    {
        $this->places = $places;
        return $this;
    }
}
