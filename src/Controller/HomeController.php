<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: self::ROUTE_PATH, name: self::ROUTE_NAME, methods: ['GET'], schemes: ['https'])]
final class HomeController extends AbstractController
{
    public const string ROUTE_PATH = '/';
    public const string ROUTE_NAME = 'home';

    /**
     * @return array
     */
    #[Template(template: 'home.html.twig')]
    public function __invoke(): array
    {
        return [];
    }
}
