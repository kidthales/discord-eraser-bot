<?php

declare(strict_types=1);

namespace App\Security\Discord\Authenticator;

use App\Controller\DiscordController;
use App\Dto\Discord\Api\PartialGuild;
use App\Entity\User;
use App\Enum\Discord\Api\BitwisePermissionFlag;
use App\HttpClient\DiscordApi;
use App\Repository\GuildRepository;
use App\Session\SessionContext;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator as BaseOAuth2Authenticator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Validator\Exception\ValidatorException;
use Throwable;
use Wohali\OAuth2\Client\Provider\DiscordResourceOwner;

final class OAuth2Authenticator extends BaseOAuth2Authenticator
{
    use UserFindableOrCreatable;

    public const string REGISTRY_CLIENT_KEY = 'discord';

    /**
     * @param ClientRegistry $registry
     * @param LoggerInterface $logger
     * @param SessionContext $sessionContext
     * @param Security $security
     * @param DiscordApi $discordApi
     * @param GuildRepository $guildRepository
     */
    public function __construct(
        private readonly ClientRegistry  $registry,
        private readonly LoggerInterface $logger,
        private readonly SessionContext  $sessionContext,
        private readonly Security        $security,
        private readonly DiscordApi      $discordApi,
        private readonly GuildRepository $guildRepository
    )
    {
    }

    /**
     * @param Request $request
     * @return bool|null
     */
    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === DiscordController::OAUTH2_CHECK_ROUTE_NAME;
    }

    /**
     * @param Request $request
     * @return Passport
     */
    public function authenticate(Request $request): Passport
    {
        $client = $this->registry->getClient(self::REGISTRY_CLIENT_KEY);
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client, $request) {
                try {
                    /** @var DiscordResourceOwner $discordResourceOwner */
                    $discordResourceOwner = $client->fetchUserFromToken($accessToken);
                } catch (Throwable $e) {
                    $this->logger->error('Encountered an error fetching Discord resource owner from access token', [
                        'exception' => FlattenException::createFromThrowable($e)
                    ]);
                    return null;
                }

                $discordId = $discordResourceOwner->getId();

                if (!$discordId) {
                    $this->logger->error('Encountered an error getting id from Discord resource owner');
                    return null;
                }

                $user = $this->findUser($discordId);
                $isSuperAdmin = $user !== null && $this->security->isGranted(User::ROLE_SUPER_ADMIN);

                $authorizedGuilds = $this->resolveAuthorizedGuilds($accessToken->getToken());

                if (empty($authorizedGuilds) && !$isSuperAdmin) {
                    return null;
                }

                $this->sessionContext->setAuthorizedGuilds($authorizedGuilds);
                $this->sessionContext->setUserInfo($discordResourceOwner);

                try {
                    return $user ?? $this->createUser($discordId);
                } catch (Throwable $e) {
                    $this->logger->critical(
                        $e instanceof ValidatorException
                            ? 'Encountered a validator error while creating user {discordId}'
                            : 'Encountered an unexpected error while creating user {discordId}',
                        [
                            'discordId' => $discordId,
                            'exception' => FlattenException::createFromThrowable($e)
                        ]
                    );
                }

                return null;
            })
        );
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $firewallName
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return $this->sessionContext->getPostAuthenticationRedirectResponse();
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());
        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * @param string $token
     * @return array<string, PartialGuild>
     */
    private function resolveAuthorizedGuilds(string $token): array
    {
        /** @var array<string, PartialGuild> $candidateAuthorizedGuilds */
        $candidateAuthorizedGuilds = [];
        foreach ($this->discordApi->withBearerToken($token)->getCurrentUserGuilds() as $partialGuild) {
            if (
                BitwisePermissionFlag::isGranted(BitwisePermissionFlag::ADMINISTRATOR, $partialGuild->permissions) ||
                BitwisePermissionFlag::isGranted(BitwisePermissionFlag::MANAGE_GUILD, $partialGuild->permissions)
            ) {
                $candidateAuthorizedGuilds[$partialGuild->id] = $partialGuild;
            }
        }

        /** @var array<string, PartialGuild> $authorizedGuilds */
        $authorizedGuilds = [];
        $availableGuilds = $this->guildRepository->findInstalledByDiscordIds(array_keys($candidateAuthorizedGuilds));
        foreach ($availableGuilds as $availableGuild) {
            $discordId = $availableGuild->getDiscordId();
            $authorizedGuilds[$discordId] = $candidateAuthorizedGuilds[$discordId];
        }

        return $authorizedGuilds;
    }
}
