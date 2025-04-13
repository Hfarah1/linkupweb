<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use App\Repository\UtilisateurRepository;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UtilisateurRepository $utilisateurRepository;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator, UtilisateurRepository $utilisateurRepository)
    {
        $this->urlGenerator = $urlGenerator;
        $this->utilisateurRepository = $utilisateurRepository;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email');
        $plainPassword = $request->request->get('pwd');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        // Fetch user by email
        $user = $this->utilisateurRepository->findOneByEmail($email);

        if (!$user || $user->getPwd() !== $plainPassword) {
            throw new CustomUserMessageAuthenticationException('Email ou mot de passe incorrect');
        }

        return new Passport(
            new UserBadge($email, function () use ($user) {
                return $user;
            }),
            new PasswordCredentials($plainPassword),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();

        if ($user && $user->getRole() && $user->getRole()->getId() === 6) {
            return new RedirectResponse($this->urlGenerator->generate('app_main'));
        }

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Default redirection for non-admin users
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
