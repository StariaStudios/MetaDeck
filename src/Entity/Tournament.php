<?php

namespace App\Entity;

use App\Repository\TournamentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TournamentRepository::class)]
class Tournament
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, DeckType>
     */
    #[ORM\ManyToMany(targetEntity: DeckType::class, inversedBy: 'tournaments')]
    private Collection $decks;

    public function __construct()
    {
        $this->decks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, DeckType>
     */
    public function getDecks(): Collection
    {
        return $this->decks;
    }

    public function addDeck(DeckType $deck): static
    {
        if (!$this->decks->contains($deck)) {
            $this->decks->add($deck);
        }

        return $this;
    }

    public function removeDeck(DeckType $deck): static
    {
        $this->decks->removeElement($deck);

        return $this;
    }
}
