<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Entity\Client;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Bourtiquev4');
    }

    public function configureMenuItems(): iterable
    {
        return [MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),
            MenuItem::section('Clients'),
            MenuItem::linkToCrud('Les clients', 'fa fa-tags', Client::class),
            MenuItem::linkToCrud('les categ', 'fa fa-tags', Categorie::class),];
    }
}
