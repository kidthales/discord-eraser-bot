<?php

namespace App\Controller\Admin;

use App\Session\SessionState;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

#[AdminDashboard(routePath: self::ROUTE_PATH, routeName: self::ROUTE_NAME)]
final class DashboardController extends AbstractDashboardController
{
    public const string ROUTE_PATH = '/_';
    public const string ROUTE_NAME = 'admin';

    /**
     * @param SessionState $sessionState
     */
    public function __construct(private readonly SessionState $sessionState)
    {
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
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
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        //yield MenuItem::section('Guilds', 'fa fa-server');
    }

    /**
     * @param UserInterface $user
     * @return UserMenu
     */
    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $userMenu = parent::configureUserMenu($user);

        $username = $this->sessionState->getUsername();

        if ($username !== null) {
            $userMenu->setName($username);
        }

        $avatarUrl = $this->sessionState->getAvatarUrl();

        if ($avatarUrl !== null) {
            $userMenu->setAvatarUrl($avatarUrl);
        }

        return $userMenu;
    }
}
