<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\DiscordController;
use App\Dto\Discord\ApplicationAuthorizedWebhookEventBody;
use App\Dto\Discord\ApplicationAuthorizedWebhookEventData;
use App\Dto\Discord\Guild as DiscordGuild;
use App\Dto\Discord\User;
use App\Dto\Discord\WebhookEventPayload;
use App\Entity\Guild as EntityGuild;
use App\Enum\Discord\WebhookEventBodyType;
use App\Enum\Discord\WebhookType;
use App\Repository\GuildRepository;
use App\Security\Discord\RequestAuthenticator;
use App\Tests\Entity\UserTest;
use App\Tests\EntityManageable;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class DiscordControllerTest extends KernelTestCase
{
    use EntityManageable;

    /**
     * @return DiscordController
     */
    private static function getSubject(): DiscordController
    {
        return self::getContainer()->get(DiscordController::class);
    }

    /**
     * @return void
     */
    public function test_webhookEvent_ping(): void
    {
        $subject = self::getSubject();

        $result = $subject->webhookEvent(
            new WebhookEventPayload(
                application_id: 'test-application-id',
                type: WebhookType::PING,
                event: null
            ),
            self::getContainer()->get(GuildRepository::class),
            self::getContainer()->get(ValidatorInterface::class),
            self::getContainer()->get(LoggerInterface::class),
            self::getContainer()->get(Security::class)
        );

        self::assertInstanceOf(JsonResponse::class, $result);
        self::assertSame($result->getContent(), 'null');
        self::assertSame($result->getStatusCode(), Response::HTTP_NO_CONTENT);
    }

    /**
     * @return void
     */
    public function test_webhookEvent_unsupported_type_ENTITLEMENT_CREATE(): void
    {
        $subject = self::getSubject();

        $event = new ApplicationAuthorizedWebhookEventBody(
            WebhookEventBodyType::EntitlementCreate,
            'test-timestamp',
            new ApplicationAuthorizedWebhookEventData(
                new User(
                    id: 'test-id',
                    username: 'test-username',
                    discriminator: 'test-discriminator',
                    global_name: null,
                    avatar: null
                ),
                []
            )
        );

        $result = $subject->webhookEvent(
            new WebhookEventPayload(
                application_id: 'test-application-id',
                type: WebhookType::Event,
                event: $event
            ),
            self::getContainer()->get(GuildRepository::class),
            self::getContainer()->get(ValidatorInterface::class),
            self::getContainer()->get(LoggerInterface::class),
            self::getContainer()->get(Security::class)
        );

        self::assertInstanceOf(JsonResponse::class, $result);
        self::assertSame($result->getContent(), 'null');
        self::assertSame($result->getStatusCode(), Response::HTTP_NO_CONTENT);
    }

    /**
     * @return void
     */
    public function test_webhookEvent_unsupported_type_QUEST_USER_ENROLLMENT(): void
    {
        $subject = self::getSubject();

        $event = new ApplicationAuthorizedWebhookEventBody(
            WebhookEventBodyType::QuestUserEnrollment,
            'test-timestamp',
            new ApplicationAuthorizedWebhookEventData(
                new User(
                    id: 'test-id',
                    username: 'test-username',
                    discriminator: 'test-discriminator',
                    global_name: null,
                    avatar: null
                ),
                []
            )
        );

        $result = $subject->webhookEvent(
            new WebhookEventPayload(
                application_id: 'test-application-id',
                type: WebhookType::Event,
                event: $event
            ),
            self::getContainer()->get(GuildRepository::class),
            self::getContainer()->get(ValidatorInterface::class),
            self::getContainer()->get(LoggerInterface::class),
            self::getContainer()->get(Security::class)
        );

        self::assertInstanceOf(JsonResponse::class, $result);
        self::assertSame($result->getContent(), 'null');
        self::assertSame($result->getStatusCode(), Response::HTTP_NO_CONTENT);
    }

    /**
     * @return void
     */
    public function test_webhookEvent_APPLICATION_AUTHORIZED_discord_agent_unauthorized(): void
    {
        $subject = self::getSubject();

        $event = new ApplicationAuthorizedWebhookEventBody(
            WebhookEventBodyType::ApplicationAuthorized,
            'test-timestamp',
            new ApplicationAuthorizedWebhookEventData(
                new User(
                    id: 'test-id',
                    username: 'test-username',
                    discriminator: 'test-discriminator',
                    global_name: null,
                    avatar: null
                ),
                []
            )
        );

        /** @var TokenStorageInterface $tokenStorage */
        $tokenStorage = self::getContainer()->get(TokenStorageInterface::class);
        /** @var UserProviderInterface $agentProvider */
        $agentProvider = self::getContainer()->get('security.user.provider.concrete.agent_provider');

        $user = $agentProvider->loadUserByIdentifier(RequestAuthenticator::DISCORD_AGENT_USER_IDENTIFIER);
        $tokenStorage->setToken(new UsernamePasswordToken($user, 'test', $user->getRoles()));

        $result = $subject->webhookEvent(
            new WebhookEventPayload(
                application_id: 'test-application-id',
                type: WebhookType::Event,
                event: $event
            ),
            self::getContainer()->get(GuildRepository::class),
            self::getContainer()->get(ValidatorInterface::class),
            self::getContainer()->get(LoggerInterface::class),
            self::getContainer()->get(Security::class)
        );

        self::assertInstanceOf(JsonResponse::class, $result);
        self::assertSame($result->getContent(), 'null');
        self::assertSame($result->getStatusCode(), Response::HTTP_NO_CONTENT);
    }

    /**
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function test_webhookEvent_APPLICATION_AUTHORIZED_integration_type_1(): void
    {
        $subject = self::getSubject();

        $user = UserTest::getSubject(175928847299117063);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $event = new ApplicationAuthorizedWebhookEventBody(
            WebhookEventBodyType::ApplicationAuthorized,
            'test-timestamp',
            new ApplicationAuthorizedWebhookEventData(
                new User(
                    id: 'test-id',
                    username: 'test-username',
                    discriminator: 'test-discriminator',
                    global_name: null,
                    avatar: null
                ),
                [],
                1
            )
        );

        /** @var TokenStorageInterface $tokenStorage */
        $tokenStorage = self::getContainer()->get(TokenStorageInterface::class);
        /** @var UserProviderInterface $userProvider */
        $userProvider = self::getContainer()->get('security.user.provider.concrete.user_provider');

        $user = $userProvider->loadUserByIdentifier('175928847299117063');
        $tokenStorage->setToken(new UsernamePasswordToken($user, 'test', $user->getRoles()));

        $result = $subject->webhookEvent(
            new WebhookEventPayload(
                application_id: 'test-application-id',
                type: WebhookType::Event,
                event: $event
            ),
            self::getContainer()->get(GuildRepository::class),
            self::getContainer()->get(ValidatorInterface::class),
            self::getContainer()->get(LoggerInterface::class),
            self::getContainer()->get(Security::class)
        );

        self::assertInstanceOf(JsonResponse::class, $result);
        self::assertSame($result->getContent(), 'null');
        self::assertSame($result->getStatusCode(), Response::HTTP_NO_CONTENT);
    }

    /**
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function test_webhookEvent_APPLICATION_AUTHORIZED_unset_guild_id(): void
    {
        $subject = self::getSubject();

        $user = UserTest::getSubject(175928847299117063);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $event = new ApplicationAuthorizedWebhookEventBody(
            WebhookEventBodyType::ApplicationAuthorized,
            'test-timestamp',
            new ApplicationAuthorizedWebhookEventData(
                new User(
                    id: 'test-id',
                    username: 'test-username',
                    discriminator: 'test-discriminator',
                    global_name: null,
                    avatar: null
                ),
                [],
                0
            )
        );

        /** @var TokenStorageInterface $tokenStorage */
        $tokenStorage = self::getContainer()->get(TokenStorageInterface::class);
        /** @var UserProviderInterface $userProvider */
        $userProvider = self::getContainer()->get('security.user.provider.concrete.user_provider');

        $user = $userProvider->loadUserByIdentifier('175928847299117063');
        $tokenStorage->setToken(new UsernamePasswordToken($user, 'test', $user->getRoles()));

        $result = $subject->webhookEvent(
            new WebhookEventPayload(
                application_id: 'test-application-id',
                type: WebhookType::Event,
                event: $event
            ),
            self::getContainer()->get(GuildRepository::class),
            self::getContainer()->get(ValidatorInterface::class),
            self::getContainer()->get(LoggerInterface::class),
            self::getContainer()->get(Security::class)
        );

        self::assertInstanceOf(JsonResponse::class, $result);
        self::assertSame($result->getContent(), 'null');
        self::assertSame($result->getStatusCode(), Response::HTTP_NO_CONTENT);
    }

    /**
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function test_webhookEvent_APPLICATION_AUTHORIZED_validator_error(): void
    {
        $subject = self::getSubject();

        $user = UserTest::getSubject(175928847299117063);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $event = new ApplicationAuthorizedWebhookEventBody(
            WebhookEventBodyType::ApplicationAuthorized,
            'test-timestamp',
            new ApplicationAuthorizedWebhookEventData(
                new User(
                    id: 'test-id',
                    username: 'test-username',
                    discriminator: 'test-discriminator',
                    global_name: null,
                    avatar: null
                ),
                [],
                0,
                new DiscordGuild(
                    id: 'failit',
                    name: null,
                    icon: null,
                    splash: null,
                    discovery_splash: null,
                    owner_id: null,
                    afk_channel_id: null,
                    afk_timeout: null,
                    verification_level: null,
                    default_message_notifications: null,
                    explicit_content_filter: null,
                    roles: null,
                    emojis: null,
                    features: [],
                    mfa_level: null,
                    application_id: null,
                    system_channel_id: null,
                    system_channel_flags: null,
                    rules_channel_id: null,
                    vanity_url_code: null,
                    description: null,
                    banner: null,
                    premium_tier: null,
                    preferred_locale: null,
                    public_updates_channel_id: null,
                    nsfw_level: null,
                    premium_progress_bar_enabled: null,
                    safety_alerts_channel_id: null
                )
            )
        );

        /** @var TokenStorageInterface $tokenStorage */
        $tokenStorage = self::getContainer()->get(TokenStorageInterface::class);
        /** @var UserProviderInterface $userProvider */
        $userProvider = self::getContainer()->get('security.user.provider.concrete.user_provider');

        $user = $userProvider->loadUserByIdentifier('175928847299117063');
        $tokenStorage->setToken(new UsernamePasswordToken($user, 'test', $user->getRoles()));

        $result = $subject->webhookEvent(
            new WebhookEventPayload(
                application_id: 'test-application-id',
                type: WebhookType::Event,
                event: $event
            ),
            self::getContainer()->get(GuildRepository::class),
            self::getContainer()->get(ValidatorInterface::class),
            self::getContainer()->get(LoggerInterface::class),
            self::getContainer()->get(Security::class)
        );

        self::assertInstanceOf(JsonResponse::class, $result);
        self::assertSame($result->getContent(), 'null');
        self::assertSame($result->getStatusCode(), Response::HTTP_NO_CONTENT);
    }

    /**
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function test_webhookEvent_APPLICATION_AUTHORIZED_create_guild(): void
    {
        $subject = self::getSubject();

        $user = UserTest::getSubject(175928847299117063);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $event = new ApplicationAuthorizedWebhookEventBody(
            WebhookEventBodyType::ApplicationAuthorized,
            'test-timestamp',
            new ApplicationAuthorizedWebhookEventData(
                new User(
                    id: 'test-id',
                    username: 'test-username',
                    discriminator: 'test-discriminator',
                    global_name: null,
                    avatar: null
                ),
                [],
                0,
                new DiscordGuild(
                    id: '175928847299117063',
                    name: null,
                    icon: null,
                    splash: null,
                    discovery_splash: null,
                    owner_id: null,
                    afk_channel_id: null,
                    afk_timeout: null,
                    verification_level: null,
                    default_message_notifications: null,
                    explicit_content_filter: null,
                    roles: null,
                    emojis: null,
                    features: [],
                    mfa_level: null,
                    application_id: null,
                    system_channel_id: null,
                    system_channel_flags: null,
                    rules_channel_id: null,
                    vanity_url_code: null,
                    description: null,
                    banner: null,
                    premium_tier: null,
                    preferred_locale: null,
                    public_updates_channel_id: null,
                    nsfw_level: null,
                    premium_progress_bar_enabled: null,
                    safety_alerts_channel_id: null
                )
            )
        );

        /** @var TokenStorageInterface $tokenStorage */
        $tokenStorage = self::getContainer()->get(TokenStorageInterface::class);
        /** @var UserProviderInterface $userProvider */
        $userProvider = self::getContainer()->get('security.user.provider.concrete.user_provider');

        $user = $userProvider->loadUserByIdentifier('175928847299117063');
        $tokenStorage->setToken(new UsernamePasswordToken($user, 'test', $user->getRoles()));

        /** @var GuildRepository $guildRepository */
        $guildRepository = self::getContainer()->get(GuildRepository::class);

        $result = $subject->webhookEvent(
            new WebhookEventPayload(
                application_id: 'test-application-id',
                type: WebhookType::Event,
                event: $event
            ),
            $guildRepository,
            self::getContainer()->get(ValidatorInterface::class),
            self::getContainer()->get(LoggerInterface::class),
            self::getContainer()->get(Security::class)
        );

        self::assertInstanceOf(JsonResponse::class, $result);
        self::assertSame($result->getContent(), 'null');
        self::assertSame($result->getStatusCode(), Response::HTTP_NO_CONTENT);

        self::assertInstanceOf(EntityGuild::class, $guildRepository->findOneByDiscordId('175928847299117063'));
    }

    /**
     * @return void
     */
    public function test_oauth2(): void
    {
        $subject = self::getSubject();
        $request = Request::create('/');
        $request->setSession(self::getContainer()->get('session.factory')->createSession());
        /** @var RequestStack $requestStack */
        $requestStack = self::getContainer()->get(RequestStack::class);
        $requestStack->push($request);

        $result = $subject->oauth2(self::getContainer()->get(ClientRegistry::class));

        self::assertInstanceOf(RedirectResponse::class, $result);
    }
}
