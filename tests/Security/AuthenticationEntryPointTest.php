<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Controller\Admin\DashboardController;
use App\Controller\DiscordController;
use App\Security\AuthenticationEntryPoint;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

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
        // TODO: move to trait...
        $request->setSession(self::getContainer()->get('session.factory')->createSession());
        /** @var RequestStack $requestStack */
        $requestStack = self::getContainer()->get(RequestStack::class);
        $requestStack->push($request);

        $result = $subject->start($request);

        self::assertInstanceOf(RedirectResponse::class, $result);
        self::assertStringEndsWith(DiscordController::OAUTH2_ROUTE_PATH, $result->getTargetUrl());
    }
}
