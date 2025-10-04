<?php

namespace App\Entity;

use App\Repository\DeckRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeckRepository::class)]
class Deck
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'decks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DeckType $deckType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $player = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, DeckCard>
     */
    #[ORM\OneToMany(targetEntity: DeckCard::class, mappedBy: 'deck')]
    private Collection $deckCards;

    public function __construct()
    {
        $this->deckCards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeckType(): ?DeckType
    {
        return $this->deckType;
    }

    public function setDeckType(?DeckType $deckType): static
    {
        $this->deckType = $deckType;

        return $this;
    }

    public function getPlayer(): ?string
    {
        return $this->player;
    }

    public function setPlayer(?string $player): static
    {
        $this->player = $player;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, DeckCard>
     */
    public function getDeckCards(): Collection
    {
        return $this->deckCards;
    }

    public function addDeckCard(DeckCard $deckCard): static
    {
        if (!$this->deckCards->contains($deckCard)) {
            $this->deckCards->add($deckCard);
            $deckCard->setDeck($this);
        }

        return $this;
    }

    public function removeDeckCard(DeckCard $deckCard): static
    {
        if ($this->deckCards->removeElement($deckCard)) {
            // set the owning side to null (unless already changed)
            if ($deckCard->getDeck() === $this) {
                $deckCard->setDeck(null);
            }
        }

        return $this;
    }
}
