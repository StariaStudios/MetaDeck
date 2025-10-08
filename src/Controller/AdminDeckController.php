<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Deck;
use App\Entity\DeckType;
use App\Form\DeckImportType;
use App\Form\DeckTypeType;
use App\Service\DeckImporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/deck', name: 'admin_deck_')]
class AdminDeckController extends AbstractController
{
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function newDeckType(Request $request, EntityManagerInterface $entityManager): Response
    {
        $deckType = new DeckType();
        $form = $this->createForm(DeckTypeType::class, $deckType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($deckType);
            $entityManager->flush();
            $this->addFlash('success', 'Deck Type created!');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/deck/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/', name: 'dashboard', methods: ['GET'])]
    public function dashboard(EntityManagerInterface $entityManager): Response
    {
        $deckTypes = $entityManager->getRepository(DeckType::class)->findAll();

        return $this->render('admin/deck/dashboard.html.twig', [
            'deckTypes' => $deckTypes,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function editDeckType(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $deckType = $entityManager->getRepository(DeckType::class)->find($id);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(DeckTypeType::class, $deckType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Deck Type updated!');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/deck/form.html.twig', [
            'form' => $form->createView(),
            'deckType' => $deckType
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function deleteDeckType(EntityManagerInterface $entityManager, Request $request, int $id): Response
    {
        $deckType = $entityManager->getRepository(DeckType::class)->find($id);

        if ($this->isCsrfTokenValid('delete_decktype'.$deckType->getId(), $request->request->get('_token'))) {
            foreach ($deckType->getDecks() as $deck) {
                foreach ($deck->getDeckCards() as $card) {
                    $entityManager->remove($card);
                }
                $entityManager->remove($deck);
            }
            $entityManager->remove($deckType);
            $entityManager->flush();
            $this->addFlash('success', 'Deck Type deleted!');
        }
        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/{id}/import', name: 'import')]
    public function importDeck(EntityManagerInterface $entityManager, Request $request, DeckImporter $importer, int $id): Response
    {
        $deckType = $entityManager->getRepository(DeckType::class)->find($id);
        $form = $this->createForm(DeckImportType::class, ['deckType' => $deckType]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $player = $form->get('player')->getData();
            $deckText = $form->get('deckText')->getData();
            $importer->import($deckType, $player, $deckText);

            $this->addFlash('success', 'Deck imported successfully!');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/deck/import.html.twig', [
            'form' => $form->createView(),
            'deckType' => $deckType,
        ]);
    }



    #[Route('/{id}', name: 'decklist')]
    public function decktypeDecklist(EntityManagerInterface $entityManager, int $id): Response
    {
        $deckType = $entityManager->getRepository(DeckType::class)->find($id);

        return $this->render('admin/deck/decklist.html.twig', [
            'deckType' => $deckType,
        ]);
    }

    #[Route('/decklist/{id}/delete', name: 'decklist_delete', methods: ['POST'])]
    public function deleteDeck(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $deck = $entityManager->getRepository(Deck::class)->find($id);

        if ($this->isCsrfTokenValid('delete_deck' . $deck->getId(), $request->request->get('_token'))) {
            foreach ($deck->getDeckCards() as $card) {
                $entityManager->remove($card);
            }
            $entityManager->remove($deck);
            $entityManager->flush();
            $this->addFlash('success', 'Deck deleted!');
        }

        return $this->redirectToRoute('admin_deck_decklist', ['id' => $deck->getDeckType()->getId()]);
    }

    #[Route('/decklist/{id}', name: 'decklist_show')]
    public function showDeck(EntityManagerInterface $entityManager, int $id): Response
    {
        $deck = $entityManager->getRepository(Deck::class)->find($id);
        return $this->render('admin/deck/show.html.twig', [
            'deck' => $deck,
        ]);
    }
}
