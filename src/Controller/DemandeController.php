<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Demande;
use App\Entity\DemandeArticle;

class DemandeController extends AbstractController
{
    #[Route('/vendeur/demande', name: 'app_demande')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $status = $request->query->get('status', 'En cours'); 
        $page = $request->query->getInt('page', 1); 
        $limit = 8; 

        $demandesQuery = $entityManager->getRepository(Demande::class)
            ->createQueryBuilder('d')
            ->where('d.statut = :status')
            ->setParameter('status', $status)
            ->orderBy('d.dateAt', 'DESC')
            ->getQuery();

        $totalDemandes = count($demandesQuery->getResult());
        $demandes = $this->getPaginatedResults($demandesQuery, $page, $limit);

        $totalPages = ceil($totalDemandes / $limit);

        return $this->render('demande/index.html.twig', [
            'demandes' => $demandes,
            'status' => $status,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    #[Route('/vendeur/detaildemande/{id}', name: 'app_demande_detail')]
    public function details(int $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        $demande = $entityManager->getRepository(Demande::class)->find($id);

        if (!$demande) {
            throw $this->createNotFoundException('Demande non trouvée');
        }

        $page = (int) $request->query->get('page', 1); 
        $limit = 8;
        $offset = ($page - 1) * $limit;

        $montants = $this->calculateMontants($demande);

        $articles = $entityManager->getRepository(DemandeArticle::class)
            ->findBy(['demande' => $demande], null, $limit, $offset);

        $totalArticles = $entityManager->getRepository(DemandeArticle::class)
            ->count(['demande' => $demande]);

        $totalPages = ceil($totalArticles / $limit);

        return $this->render('demande/detaildemande.html.twig', [
            'demande' => $demande,
            'client' => $demande->getClient(), 
            'articles' => $articles,
            'montant' => $montants['total'],
            'montantVerse' => $montants['verse'],
            'montantRestant' => $montants['restant'],
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    // Méthode pour récupérer les résultats paginés
    private function getPaginatedResults($queryBuilder, int $page, int $limit)
    {
        return $queryBuilder
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getResult();
    }

    // Méthode pour calculer les montants total, versé et restant
    private function calculateMontants(Demande $demande): array
    {
        $montantTotal = $demande->getMontant();
        $montantVerse = 0;
        $montantRestant = $montantTotal - $montantVerse;

        foreach ($demande->getDemandeArticles() as $demandeArticle) {
            $montantTotal += $demandeArticle->getQuantite() * $demandeArticle->getArticle()->getPrix();
        }

        $montantVerse += $demande->getMontant();
        $montantRestant = $montantTotal - $montantVerse;

        return [
            'total' => $montantTotal,
            'verse' => $montantVerse,
            'restant' => $montantRestant
        ];
    }
}
