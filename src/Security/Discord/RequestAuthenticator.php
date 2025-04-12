<?php

declare(strict_types=1);

namespace App\Security\Discord;

use App\Controller\DiscordController;
use App\Entity\User;
use App\Enum\Discord\WebhookEventBodyType;
use App\Enum\Discord\WebhookType;
use App\Repository\UserRepository;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

final class RequestAuthenticator extends AbstractAuthenticator
{
    public const string DISCORD_AGENT_USER_IDENTIFIER = 'discord_agent';

    /**
     * @param RequestValidator $requestValidator
     * @param LoggerInterface $logger
     * @param UserRepository $userRepository
     * @param ValidatorInterface $validator
     */
    public function __construct(
        private readonly RequestValidator   $requestValidator,
        private readonly LoggerInterface    $logger,
        private readonly UserRepository     $userRepository,
        private readonly ValidatorInterface $validator
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

        // TODO: or interaction command...
        if ($type === WebhookType::Event->value) {
            if (isset($payload->all('event')['type'])) {
                // Webhook event!
                $event = $payload->all('event');

                switch ($event['type']) {
                    case WebhookEventBodyType::ApplicationAuthorized->value:
                        if (isset($event['data']['user']['id'])) {
                            $discordId = $event['data']['user']['id'];

                            try {
                                return $this->findOrCreateUser($discordId)->getUserIdentifier();
                            } catch (Throwable $e) {
                                $this->logger->critical(
                                    $e instanceof ValidatorException
                                        ? 'Encountered a validator error while creating user {discordId}'
                                        : 'Encountered an unexpected error while finding or creating user {discordId}',
                                    [
                                        'discordId' => $discordId,
                                        'exception' => FlattenException::createFromThrowable($e)
                                    ]
                                );
                            }
                        } else {
                            $this->logger->error('Unset user id in ' . $event['type'] . ' webhook event data');
                        }
                        break;
                    case WebhookEventBodyType::EntitlementCreate->value:
                    case WebhookEventBodyType::QuestUserEnrollment->value:
                        $this->logger->error('Unsupported webhook event type ' . $event['type']);
                        break;
                    default:
                        $this->logger->error('Unknown webhook event type ' . $event['type']);
                        break;
                }
            }

            $this->logger->info('Falling back to user ' . self::DISCORD_AGENT_USER_IDENTIFIER);
        } else if ($type !== WebhookType::PING->value) {
            $this->logger->error('Unknown webhook type ' . $type);
            $this->logger->info('Falling back to user ' . self::DISCORD_AGENT_USER_IDENTIFIER);
        }

        return self::DISCORD_AGENT_USER_IDENTIFIER;
    }

    /**
     * @param string $discordId
     * @return User
     * @throws ValidatorException
     */
    private function findOrCreateUser(string $discordId): User
    {
        $user = $this->userRepository->findOneByDiscordId($discordId);

        if ($user === null) {
            $user = new User();
            $user->setDiscordId($discordId);

            $errors = $this->validator->validate($user);
            if (count($errors) > 0) {
                throw new ValidatorException((string)$errors);
            }

            $this->userRepository->add($user, true);
        }

        return $user;
    }
}
