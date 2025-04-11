<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\OffreRepository;

#[ORM\Entity(repositoryClass: OffreRepository::class)]
#[ORM\Table(name: 'offre')]
class Offre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_offre = null;

    public function getId_offre(): ?int
    {
        return $this->id_offre;
    }

    public function setId_offre(int $id_offre): self
    {
        $this->id_offre = $id_offre;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'offres')]
    #[ORM\JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id')]
    #[Assert\NotNull(message: "L'utilisateur est requis")]
    private ?User $user = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: "Le titre ne peut pas être vide")]
    #[Assert\Length(
        min: 5,
        max: 100,
        minMessage: "Le titre doit faire au moins {{ limit }} caractères",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $titre = null;

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = trim($titre);
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: "Le gouvernorat ne peut pas être vide")]
    #[Assert\Length(
        max: 50,
        maxMessage: "Le nom du gouvernorat ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $gouvernorat = null;

    public function getGouvernorat(): ?string
    {
        return $this->gouvernorat;
    }

    public function setGouvernorat(string $gouvernorat): self
    {
        $this->gouvernorat = trim($gouvernorat);
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\Length(
        max: 50,
        maxMessage: "Le nom de la ville ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $ville = null;

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = trim($ville);
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: false)]
    #[Assert\NotBlank(message: "La description ne peut pas être vide")]
    #[Assert\Length(
        min: 20,
        max: 2000,
        minMessage: "La description doit faire au moins {{ limit }} caractères",
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $description = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = trim($description);
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: "Le statut ne peut pas être vide")]
    #[Assert\Choice(
        choices: ['ouvert', 'fermé', 'en_attente'],
        message: "Choisissez un statut valide (ouvert, fermé ou en_attente)"
    )]
    private ?string $statut = null;

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = strtolower(trim($statut));
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: "L'organisation ne peut pas être vide")]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: "Le nom de l'organisation doit faire au moins {{ limit }} caractères",
        maxMessage: "Le nom de l'organisation ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $organisation = null;

    public function getOrganisation(): ?string
    {
        return $this->organisation;
    }

    public function setOrganisation(string $organisation): self
    {
        $this->organisation = trim($organisation);
        return $this;
    }

    #[ORM\Column(type: 'blob', nullable: false)]
    private $organisationLogo = null;

    public function getOrganisationLogo(): ?string
    {
        if ($this->organisationLogo === null) {
            return null;
        }
        
        // Convert resource to string if needed
        if (is_resource($this->organisationLogo)) {
            $this->organisationLogo = stream_get_contents($this->organisationLogo);
        }
        return $this->organisationLogo;
    }

    public function setOrganisationLogo($organisationLogo): self
    {
        $this->organisationLogo = $organisationLogo;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: "La catégorie ne peut pas être vide")]
    #[Assert\Length(
        max: 50,
        maxMessage: "La catégorie ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $categorie = null;

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): self
    {
        $this->categorie = trim($categorie);
        return $this;
    }

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    #[Assert\Type(
        type: 'numeric',
        message: "Le salaire doit être un nombre"
    )]
    #[Assert\PositiveOrZero(message: "Le salaire doit être zéro ou positif")]
    private ?float $salaire = null;

    public function getSalaire(): ?float
    {
        return $this->salaire;
    }

    public function setSalaire(?float $salaire): self
    {
        $this->salaire = $salaire >= 0 ? $salaire : null;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: false)]
    #[Assert\NotNull(message: "La date de publication est requise")]
    #[Assert\Type("\DateTimeInterface")]
    private ?\DateTimeInterface $date_publication = null;

    public function getDate_publication(): ?\DateTimeInterface
    {
        return $this->date_publication;
    }

    public function setDate_publication(\DateTimeInterface $date_publication): self
    {
        $this->date_publication = $date_publication;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: "Le type de contrat ne peut pas être vide")]
    #[Assert\Choice(
        choices: ['Bénévolat','Contrat', 'Mission ','CDD Événementiel','CDI Saisonnier',  'Freelance', 'Stage'],
        message: "Choisissez un type de contrat valide"
    )]
    private ?string $type_contrat = null;

    public function getType_contrat(): ?string
    {
        return $this->type_contrat;
    }

    public function setType_contrat(string $type_contrat): self
    {
        $this->type_contrat = trim($type_contrat);
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(
        max: 1000,
        maxMessage: "Les compétences requises ne peuvent pas dépasser {{ limit }} caractères"
    )]
    private ?string $competences_requises = null;

    public function getCompetences_requises(): ?string
    {
        return $this->competences_requises;
    }

    public function setCompetences_requises(?string $competences_requises): self
    {
        $this->competences_requises = $competences_requises ? trim($competences_requises) : null;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Candidature::class, mappedBy: 'offre')]
    private Collection $candidatures;

    public function __construct()
    {
        $this->candidatures = new ArrayCollection();
        $this->date_publication = new \DateTime();
        $this->statut = 'ouvert';
    }

    /**
     * @return Collection<int, Candidature>
     */
    public function getCandidatures(): Collection
    {
        if (!$this->candidatures instanceof Collection) {
            $this->candidatures = new ArrayCollection();
        }
        return $this->candidatures;
    }

    public function addCandidature(Candidature $candidature): self
    {
        if (!$this->getCandidatures()->contains($candidature)) {
            $this->getCandidatures()->add($candidature);
        }
        return $this;
    }

    public function removeCandidature(Candidature $candidature): self
    {
        $this->getCandidatures()->removeElement($candidature);
        return $this;
    }

    public function getIdOffre(): ?int
    {
        return $this->id_offre;
    }

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->date_publication;
    }

    public function setDatePublication(\DateTimeInterface $date_publication): static
    {
        $this->date_publication = $date_publication;
        return $this;
    }

    public function getTypeContrat(): ?string
    {
        return $this->type_contrat;
    }

    public function setTypeContrat(string $type_contrat): static
    {
        $this->type_contrat = $type_contrat;
        return $this;
    }

    public function getCompetencesRequises(): ?string
    {
        return $this->competences_requises;
    }

    public function setCompetencesRequises(?string $competences_requises): static
    {
        $this->competences_requises = $competences_requises;
        return $this;
    }
}