<?php

declare(strict_types=1);

namespace App\Security;

use App\Controller\Admin\DashboardController;
use App\Controller\DiscordController;
use App\Session\SessionState;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

final readonly class AuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param SessionState $sessionState
     */
    public function __construct(private UrlGeneratorInterface $urlGenerator, private SessionState $sessionState)
    {
    }

    /**
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return Response
     */
    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        $this->sessionState->setPostAuthenticationRedirect(
            $request->attributes->get('_route', DashboardController::ROUTE_NAME),
            $request->attributes->get('_route_params', [])
        );

        return new RedirectResponse($this->urlGenerator->generate(DiscordController::OAUTH2_ROUTE_NAME));
    }
}
