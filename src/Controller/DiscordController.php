<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Discord\WebhookEventPayload;
use App\Entity\Guild;
use App\Entity\User;
use App\Enum\Discord\WebhookEventBodyType;
use App\Enum\Discord\WebhookType;
use App\Repository\GuildRepository;
use App\Security\Discord\OAuth2Authenticator;
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
     * @param WebhookEventPayload $payload
     * @param GuildRepository $guildRepository
     * @param ValidatorInterface $validator
     * @param LoggerInterface $logger
     * @param Security $security
     * @return JsonResponse
     */
    #[Route(
        path: self::WEBHOOK_EVENT_ROUTE_PATH,
        name: self::WEBHOOK_EVENT_ROUTE_NAME,
        methods: ['POST'],
        stateless: true
    )]
    public function webhookEvent(
        #[MapRequestPayload] WebhookEventPayload $payload,
        GuildRepository                          $guildRepository,
        ValidatorInterface                       $validator,
        LoggerInterface                          $logger,
        Security                                 $security
    ): JsonResponse
    {
        if ($payload->type === WebhookType::Event) {
            switch ($payload->event->type) {
                case WebhookEventBodyType::ApplicationAuthorized:
                    if ($security->isGranted(User::ROLE_USER)) {
                        if ($payload->event->data->integration_type === 0) {
                            if (isset($payload->event->data->guild->id)) {
                                $guild = $guildRepository->findOneByDiscordId($payload->event->data->guild->id);

                                if ($guild === null) {
                                    $guild = new Guild();
                                    $guild->setDiscordId($payload->event->data->guild->id);
                                }

                                $guild->setInstalled(true);

                                $errors = $validator->validate($guild);
                                if (count($errors) === 0) {
                                    try {
                                        $guildRepository->add($guild, true);
                                        // @codeCoverageIgnoreStart
                                    } catch (Throwable $e) {
                                        $logger->critical(
                                            'Encountered an unexpected error while upserting guild {discordId}',
                                            [
                                                'discordId' => $payload->event->data->guild->id,
                                                'exception' => FlattenException::createFromThrowable($e)
                                            ]
                                        );
                                    }
                                    // @codeCoverageIgnoreEnd
                                } else {
                                    $logger->critical(
                                        'Encountered a validator error while creating guild {discordId}',
                                        [
                                            'discordId' => $payload->event->data->guild->id,
                                            'errors' => (array)$errors,
                                        ]
                                    );
                                }
                            } else {
                                $logger->error(
                                    'Unset guild id in ' . $payload->event->type->value . ' webhook event data'
                                );
                            }
                        } else {
                            $logger->error(
                                'Unsupported integration type {integrationType} in ' . $payload->event->type->value . ' webhook event data',
                                [
                                    'integrationType' => $payload->event->data->integration_type
                                ]
                            );
                        }
                    } else {
                        $logger->warning('Access denied for user {userIdentifier}. Did user creation fail?', [
                            'userIdentifier' => $security->getToken()->getUserIdentifier()
                        ]);
                    }
                    break;
                case WebhookEventBodyType::EntitlementCreate:
                case WebhookEventBodyType::QuestUserEnrollment:
                    $logger->error('Unsupported webhook event type ' . $payload->event->type->value);
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
        return $registry->getClient(OAuth2Authenticator::REGISTRY_CLIENT_KEY)->redirect(['identify']);
    }

    /**
     * @return void
     * @codeCoverageIgnore
     */
    #[Route(path: self::OAUTH2_CHECK_ROUTE_PATH, name: self::OAUTH2_CHECK_ROUTE_NAME, methods: ['GET'])]
    public function oauth2Check(): void
    {
    }
}
