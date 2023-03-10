<?php

namespace App\Controller\Admin;

use App\Classe\RegisterEmailService;
use App\Classe\StateLivraisonEmailService;
use App\Classe\StatePreparationEmailService;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use JetBrains\PhpStorm\NoReturn;

class OrderCrudController extends AbstractCrudController
{
    private EntityManagerInterface $em;
    private AdminUrlGenerator $adminUrlGenerator;
    private StatePreparationEmailService $emailServicePreparation;
    private StateLivraisonEmailService $emailServiceLivraison;

    public function __construct(EntityManagerInterface $em, AdminUrlGenerator $crudUrlGenerator, StatePreparationEmailService  $emailServicePreparation, StateLivraisonEmailService $emailServiceLivraison)
    {
        $this->em = $em;
        $this->adminUrlGenerator = $crudUrlGenerator;
        $this->emailServicePreparation = $emailServicePreparation;
        $this->emailServiceLivraison = $emailServiceLivraison;
    }
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Action|Actions $actions): Actions
    {
        $updatePreparation = Action::new('updatePreparation', 'Préparation en cours', 'fas fa-box-open') ->linkToCrudAction('updatePreparation') ->addCssClass('btn btn-primary');
        $updateLivraison= Action::new('updateLivraison', 'Livraison en cours', 'fas fa-truck') ->linkToCrudAction('updateLivraison') ->addCssClass('btn btn-primary');

        return parent::configureActions($actions)
            ->add('detail', $updatePreparation)
            ->add('detail', $updateLivraison)
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                return $action
                    ->setIcon('fas fa-eye')
                    ->setLabel('VOIR LES DÉTAILS')
                    ->setCssClass('btn btn-warning btn-sm text-white')
                    ->setHtmlAttributes(['title' => 'Voir la commande']);
            });

    }

    public function updatePreparation(AdminContext $context): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(2);
        $this->em->flush();

        // On envoie l'email à l'utilisateur à savoir si sa commande est en cours de préparation
        $this->emailServicePreparation->sendOrderPreparationEmail($order);

        //$this->addFlash('success', 'La commande est en cours de préparation.', ['class' => 'alert-green']);
        $this->addFlash('success', sprintf('La commande %s est en cours de préparation.', $order->getRef()), ['class' => 'alert-green']);

        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction(Crud::PAGE_INDEX)
            ->setEntityId($order->getId())
            ->generateUrl();

        return $this->redirect($url);
    }

    public function updateLivraison(AdminContext $context): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(3);
        $this->em->flush();

        // On envoie l'email à l'utilisateur à savoir si sa commande est en cours livraison
        $this->emailServiceLivraison->sendOrderLivraisonEmail($order);

        //$this->addFlash('success', 'La commande est en cours de préparation.', ['class' => 'alert-green']);
        $this->addFlash('success', sprintf('La commande %s est en cours de livraison.', $order->getRef()), ['class' => 'alert-info']);

        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction(Crud::PAGE_INDEX)
            ->setEntityId($order->getId())
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('user.getFullName', 'Clients'),
            TextEditorField::new('delivery', 'Adresse de livraison')
                ->onlyOnDetail()
                ->addCssClass('text-infos'),
            TextField::new('carrierName', 'Transporteur'),
            MoneyField::new('total', 'Total produit')->setCurrency('EUR'),
            MoneyField::new('carrierPrice', 'Frais de transport')->setCurrency('EUR'),
            ChoiceField::new('state', 'Statut')
                ->setChoices([
                    'Non payé' => 0,
                    'Payé' => 1,
                    'Préparation en cours' => 2,
                    'Livraison en cours' => 3,
                ])
                ->setCustomOption('widget_class', 'text-green'),
            DateTimeField::new('createdAt', 'Date de commande'),
            ArrayField::new('orderDetails', 'Produits achetés')->hideOnIndex(),
        ];
    }

}
