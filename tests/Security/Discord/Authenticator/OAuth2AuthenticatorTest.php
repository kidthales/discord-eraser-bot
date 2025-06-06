<?php

declare(strict_types=1);

namespace App\Tests\Security\Discord\Authenticator;

use App\Controller\DiscordController;
use App\Security\AuthenticationEntryPoint;
use App\Security\Discord\Authenticator\OAuth2Authenticator;
use App\Tests\EntityManageable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

final class OAuth2AuthenticatorTest extends KernelTestCase
{
    use EntityManageable;

    /**
     * @return OAuth2Authenticator
     */
    private static function getSubject(): OAuth2Authenticator
    {
        return self::getContainer()->get(OAuth2Authenticator::class);
    }

    /**
     * @return void
     */
    public function test_supports(): void
    {
        $subject = self::getSubject();
        $request = Request::create('/');
        $request->attributes->set('_route', DiscordController::OAUTH2_CHECK_ROUTE_NAME);

        self::assertTrue($subject->supports($request));

        $request->attributes->set('_route', 'not_supported');

        self::assertFalse($subject->supports($request));
    }

    /**
     * @return void
     */
    public function test_onAuthenticationSuccess(): void
    {
        $subject = self::getSubject();
        $request = Request::create('/');
        // TODO: move to trait...
        $request->setSession(self::getContainer()->get('session.factory')->createSession());
        /** @var RequestStack $requestStack */
        $requestStack = self::getContainer()->get(RequestStack::class);
        $requestStack->push($request);

        $result = $subject->onAuthenticationSuccess($request, new NullToken(), 'test');

        self::assertInstanceOf(RedirectResponse::class, $result);
    }

    /**
     * @return void
     */
    public function test_onAuthenticationFailure(): void
    {
        $subject = self::getSubject();
        $request = Request::create('/');
        $exception = new AuthenticationException();

        $result = $subject->onAuthenticationFailure($request, $exception);

        self::assertInstanceOf(Response::class, $result);
        self::assertEquals(Response::HTTP_FORBIDDEN, $result->getStatusCode());
    }
}
