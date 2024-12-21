<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ClientRepository;
use App\Repository\ArticleRepository;

class DashboardController extends AbstractController
{
    #[Route('/proprio/dashboard', name: 'app_dashboard')]
    public function index(ClientRepository $clientRepository, ArticleRepository $articleRepository, Request $request): Response
    {
        // Pagination des clients
        $pageClients = $request->query->getInt('page_clients', 1);
        $limitClients = 5;
        $clients = $this->getPaginatedItems($clientRepository, $pageClients, $limitClients);

        // Pagination des articles
        $pageArticles = $request->query->getInt('page_articles', 1);
        $limitArticles = 5;
        $articles = $this->getPaginatedItems($articleRepository, $pageArticles, $limitArticles);

        // Nombre total d'éléments
        $totalClients = $clientRepository->count([]);
        $totalArticles = $articleRepository->count([]);

        // Nombre de pages pour chaque entité
        $nbrePagesClients = ceil($totalClients / $limitClients);
        $nbrePagesArticles = ceil($totalArticles / $limitArticles);

        return $this->render('dashboard/index.html.twig', [
            'clients' => $clients,
            'articles' => $articles,
            'nbrePagesClients' => $nbrePagesClients,
            'nbrePagesArticles' => $nbrePagesArticles,
            'pageClients' => $pageClients,
            'pageArticles' => $pageArticles,
        ]);
    }

    // Méthode pour gérer la pagination des éléments (clients ou articles)
    private function getPaginatedItems($repository, int $page, int $limit)
    {
        return $repository->findBy([], null, $limit, ($page - 1) * $limit);
    }
}
