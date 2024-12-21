<?php

namespace App\Controller;

use App\Repository\PaiementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaiementController extends AbstractController
{
    private PaiementRepository $paiementRepository;

    public function __construct(PaiementRepository $paiementRepository)
    {
        $this->paiementRepository = $paiementRepository;
    }

    #[Route('/paiement', name: 'app_paiement')]
    public function index(): Response
    {
        // Récupérer la liste des paiements depuis la base de données
        $paiements = $this->paiementRepository->findAll();

        return $this->render('paiement/index.html.twig', [
            'paiements' => $paiements,
        ]);
    }
}
