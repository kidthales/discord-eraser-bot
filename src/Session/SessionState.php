<?php

declare(strict_types=1);

namespace App\Session;

use App\Controller\Admin\DashboardController;
use App\Dto\Discord\Api\PartialGuild;
use App\Dto\Discord\UserInfo;
use LogicException;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Throwable;
use Wohali\OAuth2\Client\Provider\DiscordResourceOwner;

final readonly class SessionState
{
    private const string AUTHORIZED_GUILDS = 'authorized_guilds';
    private const string POST_AUTHENTICATION_REDIRECT_ROUTE_NAME = 'post_authentication_redirect_route_name';
    private const string POST_AUTHENTICATION_REDIRECT_ROUTE_PARAMS = 'post_authentication_redirect_route_params';
    private const string USER_INFO = 'user_info';

    /**
     * @param RequestStack $requestStack
     * @param RouterInterface $router
     * @param NormalizerInterface $normalizer
     * @param DenormalizerInterface $denormalizer
     */
    public function __construct(
        private RequestStack $requestStack,
        private RouterInterface $router,
        private NormalizerInterface $normalizer,
        private DenormalizerInterface $denormalizer
    )
    {
    }

    /**
     * @param string $routeName
     * @param array $routeParams
     * @return void
     */
    public function setPostAuthenticationRedirectResponse(string $routeName, array $routeParams): void
    {
        $session = $this->getSession();
        $session->set(self::POST_AUTHENTICATION_REDIRECT_ROUTE_NAME, $routeName);
        $session->set(self::POST_AUTHENTICATION_REDIRECT_ROUTE_PARAMS, $routeParams);
    }

    /**
     * @return RedirectResponse
     */
    public function getPostAuthenticationRedirectResponse(): RedirectResponse
    {
        $session = $this->getSession();
        return new RedirectResponse($this->router->generate(
            $session->remove(self::POST_AUTHENTICATION_REDIRECT_ROUTE_NAME)
                ?? DashboardController::ROUTE_NAME,
            $session->remove(self::POST_AUTHENTICATION_REDIRECT_ROUTE_PARAMS) ?? []
        ));
    }

    /**
     * @param array<string, PartialGuild> $authorizedGuilds
     * @return void
     */
    public function setAuthorizedGuilds(array $authorizedGuilds): void
    {
        try {
            $this->getSession()->set(self::AUTHORIZED_GUILDS, $this->normalizer->normalize($authorizedGuilds));
        } catch (Throwable $e) {
            throw new RuntimeException(
                message: 'Error normalizing authorized guilds into session storage',
                previous: $e
            );
        }
    }

    /**
     * @param DiscordResourceOwner $discordResourceOwner
     * @return void
     */
    public function setUserInfo(DiscordResourceOwner $discordResourceOwner): void
    {
        $this->getSession()->set(self::USER_INFO, $discordResourceOwner->toArray());
    }

    /**
     * @return UserInfo
     */
    public function getUserInfo(): UserInfo
    {
        $rawUserInfo = $this->getSession()->get(self::USER_INFO);

        if ($rawUserInfo === null) {
            throw new LogicException('User info not set in session storage');
        }

        try {
            return $this->denormalizer->denormalize($rawUserInfo, UserInfo::class);
        } catch (Throwable $e) {
            throw new RuntimeException(
                message: 'Error denormalizing user info from session storage',
                previous: $e
            );
        }
    }

    /**
     * @return SessionInterface
     */
    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }
}
