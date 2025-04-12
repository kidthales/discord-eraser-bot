<?php

declare(strict_types=1);

namespace App\Tests\Security\Discord;

use App\Controller\DiscordController;
use App\Enum\Discord\WebhookEventBodyType;
use App\Enum\Discord\WebhookType;
use App\Security\Discord\RequestAuthenticator;
use App\Security\Discord\RequestValidator;
use App\Tests\EntityManageable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Throwable;

final class RequestAuthenticatorTest extends KernelTestCase
{
    use EntityManageable;

    /**
     * @return RequestAuthenticator
     */
    static private function getSubject(): RequestAuthenticator
    {
        return self::getContainer()->get(RequestAuthenticator::class);
    }

    /**
     * @return void
     */
    public function test_supports(): void
    {
        $subject = self::getSubject();
        $request = Request::create('/');

        self::assertFalse($subject->supports($request));

        $request->attributes->set('_route', DiscordController::WEBHOOK_EVENT_ROUTE_NAME);

        self::assertTrue($subject->supports($request));
    }

    /**
     * @return void
     */
    public function test_authenticate_success_ping(): void
    {
        $validator = self::createMock(RequestValidator::class);
        $validator->expects(self::once())
            ->method('validate')
            ->willReturn(true);
        self::getContainer()->set(RequestValidator::class, $validator);

        $subject = self::getSubject();
        $request = Request::create(uri: '/', content: '{"type":' . WebhookType::PING->value . '}');
        $request->attributes->set('_route', DiscordController::WEBHOOK_EVENT_ROUTE_NAME);

        $result = $subject->authenticate($request);

        self::assertInstanceOf(SelfValidatingPassport::class, $result);
        self::assertSame('discord_agent', $result->getBadge(UserBadge::class)->getUserIdentifier());
    }

    /**
     * @return void
     */
    public function test_authenticate_success_unset_type_fallback_discord_agent(): void
    {
        $validator = self::createMock(RequestValidator::class);
        $validator->expects(self::once())
            ->method('validate')
            ->willReturn(true);
        self::getContainer()->set(RequestValidator::class, $validator);

        $subject = self::getSubject();
        $request = Request::create(uri: '/', content: '{"type":' . WebhookType::Event->value . '}');
        $request->attributes->set('_route', DiscordController::WEBHOOK_EVENT_ROUTE_NAME);

        $result = $subject->authenticate($request);

        self::assertInstanceOf(SelfValidatingPassport::class, $result);
        self::assertSame('discord_agent', $result->getBadge(UserBadge::class)->getUserIdentifier());
    }

    /**
     * @return void
     */
    public function test_authenticate_success_event_type_APPLICATION_AUTHORIZED_unset_user_id_fallback_discord_agent(): void
    {
        $validator = self::createMock(RequestValidator::class);
        $validator->expects(self::once())
            ->method('validate')
            ->willReturn(true);
        self::getContainer()->set(RequestValidator::class, $validator);

        $subject = self::getSubject();
        $request = Request::create(uri: '/', content: '{"type":' . WebhookType::Event->value . ',"event":{"type":"' . WebhookEventBodyType::ApplicationAuthorized->value . '"}}');
        $request->attributes->set('_route', DiscordController::WEBHOOK_EVENT_ROUTE_NAME);

        $result = $subject->authenticate($request);

        self::assertInstanceOf(SelfValidatingPassport::class, $result);
        self::assertSame('discord_agent', $result->getBadge(UserBadge::class)->getUserIdentifier());
    }

    /**
     * @return void
     */
    public function test_authenticate_success_event_type_APPLICATION_AUTHORIZED_validator_error_fallback_discord_agent(): void
    {
        $validator = self::createMock(RequestValidator::class);
        $validator->expects(self::once())
            ->method('validate')
            ->willReturn(true);
        self::getContainer()->set(RequestValidator::class, $validator);

        $subject = self::getSubject();
        $request = Request::create(
            uri: '/',
            content: '{"type":' . WebhookType::Event->value . ',"event":{"type":"' . WebhookEventBodyType::ApplicationAuthorized->value . '","data":{"user":{"id":"failit"}}}}'
        );
        $request->attributes->set('_route', DiscordController::WEBHOOK_EVENT_ROUTE_NAME);

        $result = $subject->authenticate($request);

        self::assertInstanceOf(SelfValidatingPassport::class, $result);
        self::assertSame('discord_agent', $result->getBadge(UserBadge::class)->getUserIdentifier());
    }

    /**
     * @return void
     */
    public function test_authenticate_success_event_type_APPLICATION_AUTHORIZED_create_user(): void
    {
        $validator = self::createMock(RequestValidator::class);
        $validator->expects(self::once())
            ->method('validate')
            ->willReturn(true);
        self::getContainer()->set(RequestValidator::class, $validator);

        $subject = self::getSubject();
        $request = Request::create(
            uri: '/',
            content: '{"type":' . WebhookType::Event->value . ',"event":{"type":"' . WebhookEventBodyType::ApplicationAuthorized->value . '","data":{"user":{"id":"175928847299117063"}}}}'
        );
        $request->attributes->set('_route', DiscordController::WEBHOOK_EVENT_ROUTE_NAME);

        $result = $subject->authenticate($request);

        self::assertInstanceOf(SelfValidatingPassport::class, $result);
        self::assertSame('175928847299117063', $result->getBadge(UserBadge::class)->getUserIdentifier());
    }

    /**
     * @return void
     */
    public function test_authenticate_success_unsupported_event_type_ENTITLEMENT_CREATE_fallback_discord_agent(): void
    {
        $validator = self::createMock(RequestValidator::class);
        $validator->expects(self::once())
            ->method('validate')
            ->willReturn(true);
        self::getContainer()->set(RequestValidator::class, $validator);

        $subject = self::getSubject();
        $request = Request::create(uri: '/', content: '{"type":' . WebhookType::Event->value . ',"event":{"type":"' . WebhookEventBodyType::EntitlementCreate->value . '"}}');
        $request->attributes->set('_route', DiscordController::WEBHOOK_EVENT_ROUTE_NAME);

        $result = $subject->authenticate($request);

        self::assertInstanceOf(SelfValidatingPassport::class, $result);
        self::assertSame('discord_agent', $result->getBadge(UserBadge::class)->getUserIdentifier());
    }

    /**
     * @return void
     */
    public function test_authenticate_success_unsupported_event_type_QUEST_USER_ENROLLMENT_fallback_discord_agent(): void
    {
        $validator = self::createMock(RequestValidator::class);
        $validator->expects(self::once())
            ->method('validate')
            ->willReturn(true);
        self::getContainer()->set(RequestValidator::class, $validator);

        $subject = self::getSubject();
        $request = Request::create(uri: '/', content: '{"type":' . WebhookType::Event->value . ',"event":{"type":"' . WebhookEventBodyType::QuestUserEnrollment->value . '"}}');
        $request->attributes->set('_route', DiscordController::WEBHOOK_EVENT_ROUTE_NAME);

        $result = $subject->authenticate($request);

        self::assertInstanceOf(SelfValidatingPassport::class, $result);
        self::assertSame('discord_agent', $result->getBadge(UserBadge::class)->getUserIdentifier());
    }

    /**
     * @return void
     */
    public function test_authenticate_success_unknown_event_type_fallback_discord_agent(): void
    {
        $validator = self::createMock(RequestValidator::class);
        $validator->expects(self::once())
            ->method('validate')
            ->willReturn(true);
        self::getContainer()->set(RequestValidator::class, $validator);

        $subject = self::getSubject();
        $request = Request::create(uri: '/', content: '{"type":' . WebhookType::Event->value . ',"event":{"type":"failit"}}');
        $request->attributes->set('_route', DiscordController::WEBHOOK_EVENT_ROUTE_NAME);

        $result = $subject->authenticate($request);

        self::assertInstanceOf(SelfValidatingPassport::class, $result);
        self::assertSame('discord_agent', $result->getBadge(UserBadge::class)->getUserIdentifier());
    }

    /**
     * @return void
     */
    public function test_authenticate_success_unknown_type_discord_agent_fallback(): void
    {
        $validator = self::createMock(RequestValidator::class);
        $validator->expects(self::once())
            ->method('validate')
            ->willReturn(true);
        self::getContainer()->set(RequestValidator::class, $validator);

        $subject = self::getSubject();
        $request = Request::create('/');
        $request->attributes->set('_route', DiscordController::WEBHOOK_EVENT_ROUTE_NAME);

        $result = $subject->authenticate($request);

        self::assertInstanceOf(SelfValidatingPassport::class, $result);
        self::assertSame('discord_agent', $result->getBadge(UserBadge::class)->getUserIdentifier());
    }

    /**
     * @return void
     */
    public function test_authenticate_throw_custom_user_message_authentication_exception(): void
    {
        self::bootKernel();

        $validator = self::createMock(RequestValidator::class);
        $validator->expects(self::once())
            ->method('validate')
            ->willReturn(false);
        self::getContainer()->set(RequestValidator::class, $validator);

        $subject = self::getSubject();
        $request = Request::create('/');
        $request->attributes->set('_route', DiscordController::WEBHOOK_EVENT_ROUTE_NAME);

        try {
            $subject->authenticate($request);
            self::fail('Custom user message authentication exception not thrown');
        } catch (Throwable $e) {
            self::assertInstanceOf(CustomUserMessageAuthenticationException::class, $e);
            self::assertSame('invalid request signature', $e->getMessage());
            self::assertNull($e->getPrevious());
        }
    }

    /**
     * @return void
     */
    public function test_authenticate_throw_custom_user_message_authentication_exception_with_previous_bad_request_http_exception(): void
    {
        self::bootKernel();

        $subject = self::getSubject();
        $request = Request::create('/');
        $request->attributes->set('_route', DiscordController::WEBHOOK_EVENT_ROUTE_NAME);

        try {
            $subject->authenticate($request);
            self::fail('Custom user message authentication exception not thrown');
        } catch (Throwable $e) {
            self::assertInstanceOf(CustomUserMessageAuthenticationException::class, $e);
            self::assertSame('invalid request signature', $e->getMessage());
            self::assertInstanceOf(BadRequestHttpException::class, $e->getPrevious());
        }
    }

    /**
     * @return void
     */
    public function test_authenticate_throw_custom_user_message_authentication_exception_with_previous_unexpected_exception(): void
    {
        self::bootKernel();

        $subject = self::getSubject();
        $request = Request::create('/');
        $request->attributes->set('_route', DiscordController::WEBHOOK_EVENT_ROUTE_NAME);
        $request->headers->set(RequestValidator::HEADER_ED25519, 'test-ed25519');
        $request->headers->set(RequestValidator::HEADER_TIMESTAMP, 'test-timestamp');

        try {
            $subject->authenticate($request);
            self::fail('Custom user message authentication exception not thrown');
        } catch (Throwable $e) {
            self::assertInstanceOf(CustomUserMessageAuthenticationException::class, $e);
            self::assertSame('invalid request signature', $e->getMessage());
            self::assertNotInstanceOf(BadRequestHttpException::class, $e->getPrevious());
            self::assertInstanceOf(Throwable::class, $e->getPrevious());
        }
    }

    /**
     * @return void
     */
    public function test_onAuthenticationSuccess(): void
    {
        self::bootKernel();

        self::assertNull(
            self::getSubject()->onAuthenticationSuccess(Request::create('/'), new NullToken(), 'test')
        );
    }

    /**
     * @return void
     */
    public function test_onAuthenticationFailure(): void
    {
        self::bootKernel();

        $subject = self::getSubject();
        $actual = $subject->onAuthenticationFailure(Request::create('/'), new AuthenticationException());

        self::assertInstanceOf(Response::class, $actual);
        self::assertSame(Response::HTTP_UNAUTHORIZED, $actual->getStatusCode());
    }
}
