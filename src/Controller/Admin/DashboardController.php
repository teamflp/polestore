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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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

    public function impersonate(User $user): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ALLOWED_TO_SWITCH');

        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $token = new UsernamePasswordToken($user, null, (array)'main', $user->getRoles());
        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_main', serialize($token));

        // Rediriger l'utilisateur vers une page de son choix
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin');
            //return new RedirectResponse($this->generateUrl('admin/admin_dashboard'));
        } else {
            return new RedirectResponse($this->generateUrl('home'));
        }
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
                ->setCssClass('btn btn-warning btn-sm text-white')
                ->setHtmlAttributes(['title' => 'Ajouter un produit']);
        });

        $actions->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
            return $action
                ->setIcon('fas fa-pen')
                ->setLabel('ÉDITER')
                ->setCssClass('btn btn-warning btn-sm text-white')
                ->setHtmlAttributes(['title' => 'Édit un produit']);
        });

        $actions->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
            return $action
                ->setIcon('fas fa-trash-alt')
                ->setLabel('SUPPRIMER')
                ->setCssClass('btn btn-warning btn-sm text-white')
                ->setHtmlAttributes(['title' => 'Supprimer un produit']);
        });

        $actions->add(Crud::PAGE_INDEX, Action::DETAIL);

        return $actions;
    }
}
