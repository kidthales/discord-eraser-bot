<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: self::ROUTE_PATH, routeName: self::ROUTE_NAME)]
class DashboardController extends AbstractDashboardController
{
    public const string ROUTE_PATH = '/_';
    public const string ROUTE_NAME = 'admin';

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

        yield MenuItem::section('Guilds', 'fa fa-server');
    }
}
