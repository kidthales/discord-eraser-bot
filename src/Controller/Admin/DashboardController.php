<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Dto\Discord\AuthorizedGuild;
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

    private const string DASHBOARD_TITLE = 'App Dashboard';

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

        if ($authorizedGuild === null && $request->attributes->get('_route') !== self::ROUTE_NAME) {
            return $this->redirectToRoute(self::ROUTE_NAME);
        }

        if ($authorizedGuild === null) {
            $context = [
                'authorizedGuilds' => []
            ];

            foreach ($this->sessionContext->getAuthorizedGuilds() as $authorizedGuild) {
                $context['authorizedGuilds'][] = [
                    'id' => $authorizedGuild->id,
                    'name' => $authorizedGuild->name,
                    'iconUrl' => $authorizedGuild->getIconUrl(),
                    'dashboardLink' => $this->generateUrl(self::ROUTE_NAME, [
                        'authorized_guild' => $authorizedGuild->id
                    ]),
                    'memberCount' => $authorizedGuild->approximateMemberCount ?? 'n/a',
                    'presenceCount' => $authorizedGuild->approximatePresenceCount ?? 'n/a'
                ];
            }

            return $this->render('admin/dashboard.html.twig', $context);
        }

        return $this->render('admin/guild-dashboard.html.twig', ['guild' => ['name' => $authorizedGuild->name]]);
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
        yield MenuItem::linkToDashboard(self::DASHBOARD_TITLE, 'fa fa-home');

        yield MenuItem::section('Guilds');
        foreach ($this->sessionContext->getAuthorizedGuilds() as $authorizedGuild) {
            yield MenuItem::subMenu(
                $authorizedGuild->name,
                'fa fa-server',
            )->setSubItems([
                MenuItem::linkToUrl(
                    'Dashboard',
                    'fa fa-table-columns',
                    $this->generateUrl(self::ROUTE_NAME, ['authorized_guild' => $authorizedGuild->id])
                )
            ]);
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
     * @return AuthorizedGuild|null
     * @throws AccessDeniedHttpException
     */
    private function getCurrentAuthorizedGuild(): ?AuthorizedGuild
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
