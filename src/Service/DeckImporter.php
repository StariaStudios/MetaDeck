<?php

namespace App\Service;

use App\Entity\Card;
use App\Entity\Deck;
use App\Entity\DeckCard;
use App\Entity\DeckType;
use Doctrine\ORM\EntityManagerInterface;

class DeckImporter
{
    public function __construct(private EntityManagerInterface $em) {}

    /**
     * Import raw deck text into a Deck
     *
     * @param DeckType $deckType
     * @param string|null $player
     * @param string $rawText
     * @return Deck
     */
    public function import(DeckType $deckType, ?string $player, string $rawText): Deck
    {
        $deck = new Deck();
        $deck->setDeckType($deckType);
        if ($player) {
            $deck->setPlayer($player);
        }
        $deck->setCreatedAt(new \DateTimeImmutable());
        $this->em->persist($deck);

        $lines = explode("\n", $rawText);
        $category = null;

        foreach ($lines as $line) {
            $line = trim($line);

            // Ignore empty lines or comments
            if (empty($line) || str_starts_with($line, '#') || str_starts_with($line, '//')) {
                continue;
            }

            // Detect categories
            if (str_starts_with($line, 'Pokémon') || str_starts_with($line, 'Pokemon')) {
                $category = 'Pokemon';
                continue;
            } elseif (str_starts_with($line, 'Trainer')) {
                $category = 'Trainer';
                continue;
            } elseif (str_starts_with($line, 'Energy')) {
                $category = 'Energy';
                continue;
            }

            // Match lines like "4 Munkidori TWM 95"
            if (preg_match('/^(\d+)\s+(.+?)\s+([A-Z0-9]+)\s+(\d+)$/', $line, $matches)) {
                $quantity = (int)$matches[1];
                $name = $matches[2];
                $setCode = $matches[3];
                $number = $matches[4];

                // Check if card exists
                $card = $this->em->getRepository(Card::class)->findOneBy([
                    'name' => $name,
                    'setCode' => $setCode,
                    'number' => $number,
                ]);

                // Create card if it doesn't exist
                if (!$card) {
                    $card = new Card();
                    $card->setName($name);
                    $card->setSetCode($setCode);
                    $card->setNumber($number);
                    $card->setCategory($category);
                    $this->em->persist($card);
                }

                // Check if DeckCard already exists for this deck
                $deckCard = $this->em->getRepository(DeckCard::class)->findOneBy([
                    'deck' => $deck,
                    'card' => $card,
                ]);

                if ($deckCard) {
                    // Add quantity if already exists
                    $deckCard->setQuantity($deckCard->getQuantity() + $quantity);
                } else {
                    $deckCard = new DeckCard();
                    $deckCard->setDeck($deck);
                    $deckCard->setCard($card);
                    $deckCard->setQuantity($quantity);
                    $this->em->persist($deckCard);
                }
            }
        }

        $this->em->flush();

        return $deck;
    }
}
