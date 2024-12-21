<?php

namespace App\Security;

use App\Repository\ClientRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginAuthentificatorAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private ClientRepository $clientRepository;

    public function __construct(UrlGeneratorInterface $urlGenerator, ClientRepository $clientRepository)
    {
        $this->urlGenerator = $urlGenerator;
        $this->clientRepository = $clientRepository;
    }

    public function authenticate(Request $request): Passport
    {
        // Accès aux données du formulaire de connexion
        $login = $request->request->get('login');
        $password = $request->request->get('password');
        $csrfToken = $request->request->get('_csrf_token');

        // Stockage du nom d'utilisateur pour la session
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $login);

        return new Passport(
            new UserBadge($login),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Vérifie si une redirection cible est définie (par exemple après un accès protégé)
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Récupération de l'utilisateur authentifié
        $user = $token->getUser();
        $roles = $user->getRoles();

        // Redirection en fonction du rôle de l'utilisateur
        if (in_array('ROLE_ADMIN', $roles)) {
            return new RedirectResponse($this->urlGenerator->generate('app_dashboard'));
        } elseif (in_array('ROLE_BOUTIQUIER', $roles)) {
            return new RedirectResponse($this->urlGenerator->generate('app_client')); // Remplacez par votre route
        } elseif (in_array('ROLE_CLIENT', $roles)) {
            // Récupération du client associé à l'utilisateur
            $client = $this->clientRepository->findOneBy(['compte' => $user]);
            if ($client) {
                $telephone = $client->getTelephone();
                return new RedirectResponse($this->urlGenerator->generate('app_dettelist', [
                    'telephone' => $telephone,
                ]));
            }
        }

        // Si aucun des cas ci-dessus ne correspond, redirection par défaut
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
