<?php

declare(strict_types=1);

namespace App\Security\Discord\Authenticator;

use App\Controller\DiscordController;
use App\Enum\Discord\InteractionType;
use App\Enum\Discord\WebhookEventBodyType;
use App\Enum\Discord\WebhookType;
use App\Security\Discord\RequestValidator;
use Psr\Log\LoggerInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Validator\Exception\ValidatorException;
use Throwable;

final class RequestAuthenticator extends AbstractAuthenticator
{
    use UserFindableOrCreatable;

    public const string DISCORD_AGENT_USER_IDENTIFIER = 'discord_agent';

    /**
     * @param RequestValidator $requestValidator
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly RequestValidator $requestValidator,
        private readonly LoggerInterface  $logger
    )
    {
    }

    /**
     * @param Request $request
     * @return bool|null
     */
    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === DiscordController::WEBHOOK_EVENT_ROUTE_NAME;
    }

    /**
     * @param Request $request
     * @return Passport
     */
    public function authenticate(Request $request): Passport
    {
        $exception = null;

        try {
            $valid = $this->requestValidator->validate($request);
        } catch (Throwable $e) {
            $exception = $e;
            $valid = false;

            if (!($e instanceof BadRequestHttpException)) {
                $this->logger->error('Request validator encountered an unexpected error', [
                    'exception' => FlattenException::createFromThrowable($exception)
                ]);
            }
        }

        if (!$valid) {
            throw new CustomUserMessageAuthenticationException(message: 'invalid request signature', previous: $exception);
        }

        return new SelfValidatingPassport(new UserBadge($this->resolveUserIdentifier($request)));
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $firewallName
     * @return null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): null
    {
        return null;
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response(
            strtr($exception->getMessageKey(), $exception->getMessageData()),
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * @param Request $request
     * @return string
     */
    private function resolveUserIdentifier(Request $request): string
    {
        $payload = $request->getPayload();
        $type = $payload->get('type');

        if ($type === WebhookType::PING->value) {
            return self::DISCORD_AGENT_USER_IDENTIFIER;
        } else {
            $webhookEventCandidate = $payload->all('event');
            if ($type === WebhookType::Event->value && isset($webhookEventCandidate['type'])) {
                return $this->resolveUserIdentifierFromWebhookEvent($webhookEventCandidate);
            }
        }

        if ($type === InteractionType::PING) {
            return self::DISCORD_AGENT_USER_IDENTIFIER;
        }

        // TODO: Discord interaction command handling...
        $this->logger->error('Unsupported Discord request type, falling back to agent', ['type' => $type]);
        return self::DISCORD_AGENT_USER_IDENTIFIER;
    }

    /**
     * @param array $event
     * @return string
     */
    private function resolveUserIdentifierFromWebhookEvent(array $event): string
    {
        switch ($event['type']) {
            case WebhookEventBodyType::ApplicationAuthorized->value:
                if (!isset($event['data']['user']['id'])) {
                    $this->logger->critical(
                        'Unset user id in APPLICATION_AUTHORIZED webhook event data, falling back to agent'
                    );
                } else if (isset($event['data']['integration_type'])) {
                    if ($event['data']['integration_type'] === 0) {
                        try {
                            // Guild installation context; Discord user will have MANAGE_GUILD permission for this
                            // guild, so persist the user in the app, if not already.
                            $discordId = $event['data']['user']['id'];
                            return $this->findOrCreateUser($discordId)->getUserIdentifier();
                        } catch (Throwable $e) {
                            $this->logger->critical(
                                (
                                    $e instanceof ValidatorException
                                        ? 'Encountered a validator error while creating user "{discordId}"'
                                        : 'Encountered an unexpected error while finding or creating user "{discordId}"'
                                ) . ', falling back to agent',
                                [
                                    'discordId' => $discordId,
                                    'exception' => FlattenException::createFromThrowable($e)
                                ]
                            );
                        }
                    } else {
                        // User installation context.
                        $this->logger->info(
                            'Unsupported Discord APPLICATION_AUTHORIZED webhook event data integration_type, falling back to agent',
                            ['integration_type' => $event['data']['integration_type']]
                        );
                    }
                } else {
                    // Unset integration_type. Usually indicates Discord OAuth2 user authorization for the app (i.e., web dashboard login attempt).
                    $discordId = $event['data']['user']['id'];
                    $user = $this->findUser($discordId);

                    if ($user !== null) {
                        return $user->getUserIdentifier();
                    }

                    $this->logger->info(
                        'With Discord APPLICATION_AUTHORIZED webhook event data unset integration_type: User "{discordId}" not found in app, falling back to agent',
                        ['discordId' => $discordId]
                    );
                }
                break;
            case WebhookEventBodyType::EntitlementCreate->value:
            case WebhookEventBodyType::QuestUserEnrollment->value:
            default:
                $this->logger->info('Unsupported Discord webhook event type, falling back to agent', [
                    'type' => $event['type']
                ]);
                break;
        }

        return self::DISCORD_AGENT_USER_IDENTIFIER;
    }
}
