<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\MaterielRepository;

#[ORM\Entity(repositoryClass: MaterielRepository::class)]
#[ORM\Table(name: 'materiels')]
class Materiel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_Materiels = null;

    public function getId_Materiels(): ?int
    {
        return $this->id_Materiels;
    }

    public function setId_Materiels(int $id_Materiels): self
    {
        $this->id_Materiels = $id_Materiels;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $nomMat = null;

    public function getNomMat(): ?string
    {
        return $this->nomMat;
    }

    public function setNomMat(string $nomMat): self
    {
        $this->nomMat = $nomMat;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: false)]
    private ?string $descriptionMat = null;

    public function getDescriptionMat(): ?string
    {
        return $this->descriptionMat;
    }

    public function setDescriptionMat(string $descriptionMat): self
    {
        $this->descriptionMat = $descriptionMat;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $categorieMat = null;

    public function getCategorieMat(): ?string
    {
        return $this->categorieMat;
    }

    public function setCategorieMat(string $categorieMat): self
    {
        $this->categorieMat = $categorieMat;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $marqueMat = null;

    public function getMarqueMat(): ?string
    {
        return $this->marqueMat;
    }

    public function setMarqueMat(string $marqueMat): self
    {
        $this->marqueMat = $marqueMat;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $referenceMat = null;

    public function getReferenceMat(): ?string
    {
        return $this->referenceMat;
    }

    public function setReferenceMat(string $referenceMat): self
    {
        $this->referenceMat = $referenceMat;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $quantiteStock = null;

    public function getQuantiteStock(): ?int
    {
        return $this->quantiteStock;
    }

    public function setQuantiteStock(int $quantiteStock): self
    {
        $this->quantiteStock = $quantiteStock;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $quantiteReservee = null;

    public function getQuantiteReservee(): ?int
    {
        return $this->quantiteReservee;
    }

    public function setQuantiteReservee(int $quantiteReservee): self
    {
        $this->quantiteReservee = $quantiteReservee;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $idEv = null;

    public function getIdEv(): ?int
    {
        return $this->idEv;
    }

    public function setIdEv(?int $idEv): self
    {
        $this->idEv = $idEv;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $imagePath = null;

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): self
    {
        $this->imagePath = $imagePath;
        return $this;
    }

    public function getIdMateriels(): ?int
    {
        return $this->id_Materiels;
    }

}
