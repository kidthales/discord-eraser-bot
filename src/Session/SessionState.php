<?php

declare(strict_types=1);

namespace App\Session;

use App\Controller\Admin\DashboardController;
use App\Dto\Discord\PartialGuild;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Throwable;

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
     */
    public function __construct(
        private RequestStack $requestStack,
        private RouterInterface $router,
        private NormalizerInterface $normalizer
    )
    {
    }

    /**
     * @param string $routeName
     * @param array $routeParams
     * @return void
     */
    public function setPostAuthenticationRedirect(string $routeName, array $routeParams): void
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
            $normalizedAuthorizedGuilds = $this->normalizer->normalize($authorizedGuilds);
        } catch (Throwable $e) {
            // TODO: logging...
            $normalizedAuthorizedGuilds = [];
        }

        $this->getSession()->set(self::AUTHORIZED_GUILDS, $normalizedAuthorizedGuilds);
    }

    /**
     * @param array{
     *      id: ?string,
     *      username: ?string,
     *      discriminator: ?string,
     *      avatar: ?string,
     *      verified: ?bool
     *  } $userInfo
     * @return void
     */
    public function setUserInfo(array $userInfo): void
    {
        $this->getSession()->set(self::USER_INFO, $userInfo);
    }

    /**
     * @return array{
     *     id: ?string,
     *     username: ?string,
     *     discriminator: ?string,
     *     avatar: ?string,
     *     verified: ?bool
     * }
     */
    public function getUserInfo(): array
    {
        return $this->getSession()->get(self::USER_INFO, []);
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->getUserInfo()['username'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getAvatarUrl(): ?string
    {
        $userInfo = $this->getUserInfo();
        return isset($userInfo['id']) && isset($userInfo['avatar'])
            ? ('https://cdn.discordapp.com/avatars/' . $userInfo['id'] . '/' . $userInfo['avatar'] . '.png')
            : null;
    }

    /**
     * @return SessionInterface
     */
    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }
}
