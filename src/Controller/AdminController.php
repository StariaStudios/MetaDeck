<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Deck;
use App\Entity\DeckType;
use App\Entity\User;
use App\Form\DeckImportType;
use App\Form\DeckTypeType;
use App\Service\DeckImporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access this page.')]
class AdminController extends AbstractController
{
    #[Route('', name: 'admin_dashboard')]
    public function dashboard(EntityManagerInterface $entityManager): Response
    {
        $deckTypes = $entityManager->getRepository(DeckType::class)->findAll();
        $decks = $entityManager->getRepository(Deck::class)->findAll();
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/index.html.twig', [
            'deckTypes' => $deckTypes,
            'users' => $users,
        ]);
    }

}
