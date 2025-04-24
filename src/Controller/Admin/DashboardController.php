<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Dto\Discord\Api\PartialGuild;
use App\Session\SessionContext;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

#[AdminDashboard(routePath: self::ROUTE_PATH, routeName: self::ROUTE_NAME)]
final class DashboardController extends AbstractDashboardController
{
    public const string ROUTE_PATH = '/_';
    public const string ROUTE_NAME = 'admin';

    /**
     * @param RequestStack $requestStack
     * @param SessionContext $sessionContext
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly SessionContext $sessionContext,
        private readonly LoggerInterface $logger
    )
    {
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $request = $this->getRequest();
        $authorizedGuild = $this->getCurrentAuthorizedGuild();

        $this->logger->debug('authorized guild id: ' . $authorizedGuild?->id);

        if ($authorizedGuild === null && $request->attributes->get('_route') !== self::ROUTE_NAME) {
            return $this->redirectToRoute(self::ROUTE_NAME);
        }

        return $this->render('admin/dashboard.html.twig');
    }

    /**
     * @return Dashboard
     */
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Discord Eraser Bot');
    }

    /**
     * @return iterable
     */
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('App Dashboard', 'fa fa-home');

        yield MenuItem::section('Authorized Guilds');
        foreach ($this->sessionContext->getAuthorizedGuilds() as $authorizedGuild) {
            yield MenuItem::linkToUrl(
                $authorizedGuild->name ?? $authorizedGuild->id,
                'fa fa-server',
                $this->generateUrl(self::ROUTE_NAME, ['authorized_guild' => $authorizedGuild->id])
            );
        }
    }

    /**
     * @param UserInterface $user
     * @return UserMenu
     */
    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $userInfo = $this->sessionContext->getUserInfo();

        return parent::configureUserMenu($user)
            ->setName($userInfo->username)
            ->setAvatarUrl($userInfo->getAvatarUrl());
    }

    /**
     * @return PartialGuild|null
     * @throws AccessDeniedHttpException
     */
    private function getCurrentAuthorizedGuild(): ?PartialGuild
    {
        $id = $this->getRequest()->query->get('authorized_guild');

        if ($id === null) {
            return null;
        }

        $authorizedGuilds = $this->sessionContext->getAuthorizedGuilds();

        if (isset($authorizedGuilds[$id])) {
            return $authorizedGuilds[$id];
        }

        throw new AccessDeniedHttpException();
    }

    /**
     * @return Request|null
     */
    private function getRequest(): ?Request
    {
        return $this->requestStack->getCurrentRequest();
    }
}
