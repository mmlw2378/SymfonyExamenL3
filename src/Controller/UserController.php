<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\ClientRepository;

class UserController extends AbstractController
{
    private ClientRepository $clientRepository;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    #[Route(path: '/', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Vérifier si l'utilisateur est déjà connecté
        $userConnect = $this->getUser();
        if ($userConnect) {
            $roles = $userConnect->getRoles();

            // Rediriger en fonction des rôles de l'utilisateur
            if (in_array('ROLE_ADMIN', $roles)) {
                return $this->redirectToRoute('app_dashboard');
            } elseif (in_array('ROLE_BOUTIQUIER', $roles)) {
                return $this->redirectToRoute('app_client');
            } elseif (in_array('ROLE_CLIENT', $roles)) {
                // Vérifier si l'utilisateur est associé à un client
                $client = $this->clientRepository->findOneBy(['compte' => $userConnect]);

                // Si le client existe, rediriger vers la page de détail de la dette
                if ($client) {
                    $telephone = $client->getTelephone();
                    return $this->redirectToRoute('app_dettelist', [
                        'telephone' => $telephone,
                    ]);
                } else {
                    // Si aucun client n'est associé, rediriger vers la page de connexion
                    $this->addFlash('error', 'Aucun client associé à cet utilisateur.');
                    return $this->redirectToRoute('app_login');
                }
            }
        }

        // Récupérer les erreurs de connexion si elles existent
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Cette méthode sera interceptée par Symfony pour gérer la déconnexion
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
