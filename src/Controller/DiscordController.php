<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Discord\WebhookEventPayload;
use App\Entity\Guild;
use App\Entity\User;
use App\Enum\Discord\WebhookEventBodyType;
use App\Enum\Discord\WebhookType;
use App\Repository\GuildRepository;
use App\Security\Discord\Authenticator\OAuth2Authenticator;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

#[Route(schemes: ['https'])]
final class DiscordController extends AbstractController
{
    public const string WEBHOOK_EVENT_ROUTE_PATH = '/discord/webhook-event';
    public const string WEBHOOK_EVENT_ROUTE_NAME = 'discord_webhook_event';

    public const string OAUTH2_ROUTE_PATH = '/discord/oauth2';
    public const string OAUTH2_ROUTE_NAME = 'discord_oauth2';

    public const string OAUTH2_CHECK_ROUTE_PATH = 'discord/oauth2-check';
    public const string OAUTH2_CHECK_ROUTE_NAME = 'discord_oauth2_check';

    /**
     * @param GuildRepository $guildRepository
     * @param ValidatorInterface $validator
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly GuildRepository    $guildRepository,
        private readonly ValidatorInterface $validator,
        private readonly LoggerInterface    $logger
    )
    {
    }

    /**
     * @param WebhookEventPayload $payload
     * @param Security $security
     * @return JsonResponse
     */
    #[Route(
        path: self::WEBHOOK_EVENT_ROUTE_PATH,
        name: self::WEBHOOK_EVENT_ROUTE_NAME,
        methods: ['POST'],
        stateless: true
    )]
    public function webhookEvent(#[MapRequestPayload] WebhookEventPayload $payload, Security $security): JsonResponse
    {
        if ($payload->type === WebhookType::Event) {
            switch ($payload->event->type) {
                case WebhookEventBodyType::ApplicationAuthorized:
                    if (isset($payload->event->data->integration_type)) {
                        if ($payload->event->data->integration_type === 0) {
                            if ($security->isGranted(User::ROLE_USER)) {
                                if (!isset($payload->event->data->guild->id)) {
                                    $this->logger->critical(
                                        'Unset guild id in APPLICATION_AUTHORIZED webhook event data for guild installation context'
                                    );
                                } else {
                                    $this->installGuild($payload->event->data->guild->id);
                                }
                            } else {
                                $this->logger->warning('Access denied for user "{userIdentifier}" while handling Discord APPLICATION_AUTHORIZED webhook event for guild installation context. Did user creation fail?', [
                                    'discordId' => $payload->event->data->user->id,
                                    'userIdentifier' => $security->getToken()->getUserIdentifier()
                                ]);
                            }
                        } else {
                            // User installation context.
                            $this->logger->info(
                                'Discord APPLICATION_AUTHORIZED webhook event data with unsupported integration_type received',
                                [
                                    'integration_type' => $payload->event->data->integration_type,
                                    'discordId' => $payload->event->data->user->id,
                                    'userIdentifier' => $security->getToken()->getUserIdentifier()
                                ]
                            );
                        }
                    } else {
                        // Unset integration_type. Usually indicates Discord OAuth2 user authorization for the app (i.e., web dashboard login attempt).
                        $this->logger->info(
                            'Discord APPLICATION_AUTHORIZED webhook event data with unset integration_type received',
                            [
                                'discordId' => $payload->event->data->user->id,
                                'userIdentifier' => $security->getToken()->getUserIdentifier()
                            ]
                        );
                    }
                    break;
                case WebhookEventBodyType::EntitlementCreate:
                case WebhookEventBodyType::QuestUserEnrollment:
                    $this->logger->info('Unsupported webhook event with type "{type}" received', [
                        'type' => $payload->event->type->value
                    ]);
                    break;
            }
        }

        return $this->json(data: null, status: Response::HTTP_NO_CONTENT);
    }

    /**
     * @param ClientRegistry $registry
     * @return Response
     */
    #[Route(path: self::OAUTH2_ROUTE_PATH, name: self::OAUTH2_ROUTE_NAME, methods: ['GET'])]
    public function oauth2(ClientRegistry $registry): Response
    {
        // TODO
        return $registry->getClient(OAuth2Authenticator::REGISTRY_CLIENT_KEY)->redirect(['identify', 'guilds']);
    }

    /**
     * @return void
     * @codeCoverageIgnore
     */
    #[Route(path: self::OAUTH2_CHECK_ROUTE_PATH, name: self::OAUTH2_CHECK_ROUTE_NAME, methods: ['GET'])]
    public function oauth2Check(): void
    {
    }

    /**
     * @param string $discordId
     * @return void
     */
    private function installGuild(string $discordId): void
    {
        $guild = $this->guildRepository->findOneByDiscordId($discordId);

        if ($guild === null) {
            $guild = new Guild();
            $guild->setDiscordId($discordId);
        }

        $guild->setInstalled(true);

        $errors = $this->validator->validate($guild);
        if (count($errors) === 0) {
            try {
                $this->guildRepository->add($guild, true);
            } catch (Throwable $e) {
                $this->logger->critical(
                    'Encountered an unexpected error while upserting guild "{discordId}"',
                    [
                        'discordId' => $discordId,
                        'exception' => FlattenException::createFromThrowable($e)
                    ]
                );
            }
        } else {
            $this->logger->critical(
                'Encountered a validator error while install guild "{discordId}"',
                [
                    'discordId' => $discordId,
                    'errors' => (array)$errors,
                ]
            );
        }
    }
}
