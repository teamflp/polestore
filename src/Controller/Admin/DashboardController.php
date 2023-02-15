<?php

namespace App\Controller\Admin;

use App\Entity\Carrier;
use App\Entity\Category;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(ProductCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
        //return $this->render('@EasyAdmin/page/login.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Boutique Poles')
            ->setFaviconPath('favicon.ico')
            ->renderContentMaximized()
            ->setTranslationDomain('admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fas fa-tachometer-alt');
        yield MenuItem::linkToRoute('Aller à la Boutique', 'fa fa-home', 'products');

        yield MenuItem::section('Gestion des articles');
        yield MenuItem::linkToCrud('Produits', 'fas fa-tags', Product::class)
            ->setDefaultSort(['id' => 'DESC'])
            ->setCssClass('text-uppercase font-weight-bold text-warning');
        yield MenuItem::linkToCrud('Catégories', 'fas fa-list', Category::class)
            ->setDefaultSort(['id' => 'DESC'])
            ->setCssClass('text-uppercase font-weight-bold text-warning');

        yield MenuItem::section('Gestion des utilisateurs');
        yield MenuItem::linkToCrud('Utilisateur', 'fas fa-users', User::class)
            ->setDefaultSort(['id' => 'DESC'])
            ->setCssClass('text-uppercase font-weight-bold text-warning')
            ->setPermission('ROLE_ADMIN');

        yield MenuItem::section('Gestion des commandes');
        yield MenuItem::linkToCrud('Commandes', 'fas fa-shopping-cart', Order::class)
            ->setDefaultSort(['id' => 'DESC'])
            ->setCssClass('text-uppercase font-weight-bold text-warning')
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Transporteurs', 'fas fa-truck', Carrier::class)
            ->setDefaultSort(['id' => 'DESC'])
            ->setCssClass('text-uppercase font-weight-bold text-warning')
            ->setPermission('ROLE_ADMIN');
    }

    public function configureActions(): Actions
    {
        $actions = parent::configureActions();

        $actions->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
            return $action
                ->setIcon('fas fa-plus')
                ->setLabel('AJOUTER')
                ->setCssClass('btn btn-success btn-lg btn-block')
                ->setHtmlAttributes(['title' => 'Ajouter un produit']);
        });

        $actions->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
            return $action
                ->setIcon('fas fa-pen')
                ->setLabel('ÉDITER')
                ->setCssClass('btn btn-primary btn-sm')
                ->setHtmlAttributes(['title' => 'Édit un produit']);
        });

        $actions->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
            return $action
                ->setIcon('fas fa-trash-alt')
                ->setLabel('SUPPRIMER')
                ->setCssClass('btn btn-sm')
                ->setHtmlAttributes(['title' => 'Supprimer un produit']);
        });

        $actions->add(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
            return $action
                ->setIcon('fas fa-eye')
                ->setLabel('VOIR')
                ->setCssClass('btn btn-info btn-sm')
                ->setHtmlAttributes(['title' => 'Voir un produit']);
        });

        return $actions;
    }
}
