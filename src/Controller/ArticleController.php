<?php
namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends AbstractController
{
    #[Route('/vendeur/article/{filter}', name: 'app_article', defaults: ['filter' => 'all'])]
    public function index(ArticleRepository $articleRepository, Request $request, string $filter): Response
    {
        $search = $request->query->get('search', ''); 
        $page = (int) $request->query->get('page', 1); 
        $limit = 8;
        
        // Initialisation des articles en fonction du filtre
        $articles = $this->getFilteredArticles($articleRepository, $filter, $search);
        
        // Logique de pagination
        $totalArticles = count($articles);
        $totalPages = ceil($totalArticles / $limit);
        $offset = ($page - 1) * $limit;
        $articlesPaginated = array_slice($articles, $offset, $limit);

        return $this->render('article/index.html.twig', [
            'articles' => $articlesPaginated, 
            'filter' => $filter,
            'search' => $search,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    private function getFilteredArticles(ArticleRepository $articleRepository, string $filter, string $search): array
    {
        // Déterminer quel ensemble d'articles récupérer en fonction du filtre
        if ($filter === 'rup') {
            return $articleRepository->findRupture(); 
        } elseif ($filter === 'dis') {
            return $articleRepository->findAvailable(); 
        } elseif (!empty($search)) {
            return $articleRepository->findByLibelle($search); 
        } else {
            return $articleRepository->findAllArticles(); 
        }
    }
}
