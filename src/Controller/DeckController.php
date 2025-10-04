<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Deck;
use App\Entity\DeckType;
use App\Form\DeckImportType;
use App\Service\DeckImporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DeckController extends AbstractController
{
    #[Route('/deck', name: 'deck_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $decks = $entityManager->getRepository(DeckType::class)->findAll();
        return $this->render('deck/index.html.twig', ['decks' => $decks]);
    }

    #[Route('/deck/{id}', name: 'deck_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager, int $id): Response
    {
        $deckType = $entityManager->getRepository(DeckType::class)->find($id);

        if (null === $deckType) {
            throw $this->createNotFoundException('Deck not found');
        }
        $deckCount = count($deckType->getDecks());
        $cardQuantities = [];

        foreach ($deckType->getDecks() as $deck) {
            foreach ($deck->getDeckCards() as $deckCard) {
                $card = $deckCard->getCard();

                if (!isset($cardQuantities[$card->getId()])) {
                    $cardQuantities[$card->getId()] = [
                        'card' => $card,
                        'total' => 0
                    ];
                }

                $cardQuantities[$card->getId()]['total'] += $deckCard->getQuantity();
            }
        }

        $averages = [];
        foreach ($cardQuantities as $data) {
            $averages[] = [
                'card' => $data['card'],
                'average' => $deckCount > 0 ? $data['total'] / $deckCount : 0
            ];
        }

        return $this->render('deck/show.html.twig', [
            'deckType' => $deckType,
            'averages' => $averages,
        ]);
    }
}
