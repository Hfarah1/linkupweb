<?php

namespace App\Entity;

    use App\Repository\UtilisateurRepository;
    use Symfony\Component\Security\Core\User\UserInterface;
    use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Component\Validator\Constraints as Assert;
    use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

    #[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
    #[UniqueEntity(fields: ['email'], message: "L'email '{{ value }}' est déjà utilisé")]
    class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
    {
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column]
        private ?int $id = null;

        #[ORM\Column(length: 30)]
        #[Assert\NotBlank(message: "Le nom est obligatoire")]
        #[Assert\Regex(
            pattern: "/^[A-Za-zÀ-ÿ\s]+$/",
            message: "Le nom ne doit contenir que des lettres et des espaces"
        )]
        private ?string $nom = null;

        #[ORM\Column(length: 40)]
        #[Assert\NotBlank(message: "Le prénom est obligatoire")]
        #[Assert\Regex(
            pattern: "/^[A-Za-zÀ-ÿ\s]+$/",
            message: "Le prénom ne doit contenir que des lettres et des espaces"
        )]
        private ?string $prenom = null;

        #[ORM\Column(length: 50, unique: true)]
        #[Assert\NotBlank(message: "L'email est obligatoire")]
        #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide")]
        private ?string $email = null;

        #[ORM\Column(length: 255)]
        #[Assert\NotBlank(message: "Le mot de passe est obligatoire")]
        private ?string $pwd = null;

        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false)]
        #[Assert\NotNull(message: "Le rôle est obligatoire")]
        private ?Role $role = null;

        #[ORM\Column(length: 8, nullable: true, unique: true)]
        #[Assert\Regex(
            pattern: "/^\d{8}$/",
            message: "Le numéro de téléphone doit contenir exactement 8 chiffres"
        )]
        private ?string $phone = null;

        public function getId(): ?int { return $this->id; }

        public function getNom(): ?string { return $this->nom; }
        public function setNom(string $nom): static { $this->nom = $nom; return $this; }

        public function getPrenom(): ?string { return $this->prenom; }
        public function setPrenom(string $prenom): static { $this->prenom = $prenom; return $this; }

        public function getEmail(): ?string { return $this->email; }
        public function setEmail(string $email): static { $this->email = $email; return $this; }

        public function getPwd(): ?string { return $this->pwd; }
        public function setPwd(string $pwd): static { $this->pwd = $pwd; return $this; }

        public function getRole(): ?Role { return $this->role; }
        public function setRole(?Role $role): static { $this->role = $role; return $this; }

        public function getPhone(): ?string { return $this->phone; }
        public function setPhone(?string $phone): static { $this->phone = $phone; return $this; }

        public function getUserIdentifier(): string
        {
            return (string) $this->email;
        }

        public function getPassword(): string
        {
            return (string) $this->pwd;
        }

        public function getRoles(): array
        {
            return ['ROLE_' . strtoupper($this->role->getName())]; 
        }

        public function eraseCredentials(): void
        {
            // If you had plain passwords stored temporarily, clear them here
        }
    }
