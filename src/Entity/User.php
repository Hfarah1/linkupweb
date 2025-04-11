<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\UserRepository;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $nom = null;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $prenom = null;

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $email = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $telephone = null;

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $password = null;

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $role = null;

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Candidature::class, mappedBy: 'user')]
    private Collection $candidatures;

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

    #[ORM\OneToMany(targetEntity: Offre::class, mappedBy: 'user')]
    private Collection $offres;

    public function __construct()
    {
        $this->candidatures = new ArrayCollection();
        $this->offres = new ArrayCollection();
    }

    /**
     * @return Collection<int, Offre>
     */
    public function getOffres(): Collection
    {
        if (!$this->offres instanceof Collection) {
            $this->offres = new ArrayCollection();
        }
        return $this->offres;
    }

    public function addOffre(Offre $offre): self
    {
        if (!$this->getOffres()->contains($offre)) {
            $this->getOffres()->add($offre);
        }
        return $this;
    }

    public function removeOffre(Offre $offre): self
    {
        $this->getOffres()->removeElement($offre);
        return $this;
    }

}
