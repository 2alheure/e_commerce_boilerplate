<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $numero = null;

    #[ORM\ManyToMany(targetEntity: Produit::class)]
    private Collection $produits;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status = null;

    public function __construct() {
        $this->produits = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $user): static {
        $this->user = $user;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static {
        $this->date = $date;

        return $this;
    }

    public function getNumero(): ?string {
        return $this->numero;
    }

    public function setNumero(string $numero): static {
        $this->numero = $numero;

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduits(): Collection {
        return $this->produits;
    }

    public function addProduit(Produit $produit): static {
        if (!$this->produits->contains($produit)) {
            $this->produits->add($produit);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): static {
        $this->produits->removeElement($produit);

        return $this;
    }

    public function getStatus(): ?Status {
        return $this->status;
    }

    public function setStatus(?Status $status): static {
        $this->status = $status;

        return $this;
    }

    function total(): float {
        $somme = 0;

        foreach ($this->produits as $produit) {
            $somme += $produit->getPrix();
            // TODO : ne pas oublier la quantité !
        }

        return $somme;
    }
}
