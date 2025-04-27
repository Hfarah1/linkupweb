<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\RencontreRepository;

#[ORM\Entity(repositoryClass: RencontreRepository::class)]
#[ORM\Table(name: 'rencontres')]
class Rencontre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
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

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $titre = null;

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $categorie_rencontre = null;

    public function getCategorie_rencontre(): ?string
    {
        return $this->categorie_rencontre;
    }

    public function setCategorie_rencontre(string $categorie_rencontre): self
    {
        $this->categorie_rencontre = $categorie_rencontre;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $lieu = null;

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $date_rencontre = null;

    public function getDate_rencontre(): ?\DateTimeInterface
    {
        return $this->date_rencontre;
    }

    public function setDate_rencontre(?\DateTimeInterface $date_rencontre = null): self
    {
        $this->date_rencontre = $date_rencontre ?: new \DateTime();
        return $this;
    }

    #[ORM\Column(type: 'blob', nullable: true)]
    private ?string $image = null;

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $image_name = null;

    public function getImage_name(): ?string
    {
        return $this->image_name;
    }

    public function setImage_name(?string $image_name): self
    {
        $this->image_name = $image_name;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $tags = null;

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(?string $tags): self
    {
        $this->tags = $tags;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $places_dispo = null;

    public function getPlaces_dispo(): ?int
    {
        return $this->places_dispo;
    }

    public function setPlaces_dispo(?int $places_dispo): self
    {
        $this->places_dispo = $places_dispo;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $budget = null;

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(?float $budget): self
    {
        $this->budget = $budget;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $bio = null;

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $statut = null;

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $age_min = null;

    public function getAge_min(): ?int
    {
        return $this->age_min;
    }

    public function setAge_min(int $age_min): self
    {
        $this->age_min = $age_min;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $age_max = null;

    public function getAge_max(): ?int
    {
        return $this->age_max;
    }

    public function setAge_max(int $age_max): self
    {
        $this->age_max = $age_max;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $distance_max = null;

    public function getDistance_max(): ?int
    {
        return $this->distance_max;
    }

    public function setDistance_max(int $distance_max): self
    {
        $this->distance_max = $distance_max;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $genre_recherche = null;

    public function getGenre_recherche(): ?string
    {
        return $this->genre_recherche;
    }

    public function setGenre_recherche(?string $genre_recherche): self
    {
        $this->genre_recherche = $genre_recherche;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $niveau_relation = null;

    public function getNiveau_relation(): ?string
    {
        return $this->niveau_relation;
    }

    public function setNiveau_relation(?string $niveau_relation): self
    {
        $this->niveau_relation = $niveau_relation;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $created_at = null;

    public function getCreated_at(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreated_at(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $latitude = null;

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $longitude = null;

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getIdRencontre(): ?int
    {
        return $this->id_rencontre;
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

    public function getCategorieRencontre(): ?string
    {
        return $this->categorie_rencontre;
    }

    public function setCategorieRencontre(string $categorie_rencontre): static
    {
        $this->categorie_rencontre = $categorie_rencontre;

        return $this;
    }

    public function getDateRencontre(): ?\DateTimeInterface
    {
        return $this->date_rencontre;
    }

    public function setDateRencontre(\DateTimeInterface $date_rencontre): static
    {
        $this->date_rencontre = $date_rencontre;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->image_name;
    }

    public function setImageName(?string $image_name): static
    {
        $this->image_name = $image_name;

        return $this;
    }

    public function getPlacesDispo(): ?int
    {
        return $this->places_dispo;
    }

    public function setPlacesDispo(?int $places_dispo): static
    {
        $this->places_dispo = $places_dispo;

        return $this;
    }

    public function getAgeMin(): ?int
    {
        return $this->age_min;
    }

    public function setAgeMin(int $age_min): static
    {
        $this->age_min = $age_min;

        return $this;
    }

    public function getAgeMax(): ?int
    {
        return $this->age_max;
    }

    public function setAgeMax(int $age_max): static
    {
        $this->age_max = $age_max;

        return $this;
    }

    public function getDistanceMax(): ?int
    {
        return $this->distance_max;
    }

    public function setDistanceMax(int $distance_max): static
    {
        $this->distance_max = $distance_max;

        return $this;
    }

    public function getGenreRecherche(): ?string
    {
        return $this->genre_recherche;
    }

    public function setGenreRecherche(?string $genre_recherche): static
    {
        $this->genre_recherche = $genre_recherche;

        return $this;
    }

    public function getNiveauRelation(): ?string
    {
        return $this->niveau_relation;
    }

    public function setNiveauRelation(?string $niveau_relation): static
    {
        $this->niveau_relation = $niveau_relation;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

}
