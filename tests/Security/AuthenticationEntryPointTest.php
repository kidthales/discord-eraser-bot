<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Controller\DiscordController;
use App\Security\AuthenticationEntryPoint;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

final class AuthenticationEntryPointTest extends KernelTestCase
{
    /**
     * @return AuthenticationEntryPoint
     */
    private static function getSubject(): AuthenticationEntryPoint
    {
        return self::getContainer()->get(AuthenticationEntryPoint::class);
    }

    /**
     * @return void
     */
    public function test_start(): void
    {
        self::bootKernel();

        $subject = self::getSubject();
        $request = Request::create('/');
        $request->setSession(self::getContainer()->get('session.factory')->createSession());

        $result = $subject->start($request);

        self::assertInstanceOf(RedirectResponse::class, $result);
        self::assertStringEndsWith(DiscordController::OAUTH2_ROUTE_PATH, $result->getTargetUrl());

        self::assertSame($request->getSession()->get(AuthenticationEntryPoint::ROUTE_NAME_SESSION_KEY), 'app_dashboard'); // TODO
        self::assertIsArray($request->getSession()->get(AuthenticationEntryPoint::ROUTE_PARAMS_SESSION_KEY));
        self::assertEmpty($request->getSession()->get(AuthenticationEntryPoint::ROUTE_PARAMS_SESSION_KEY));
    }
}
