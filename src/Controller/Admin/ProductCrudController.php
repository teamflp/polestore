<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }


    public function configureFields(string $pageName): iterable
    {
        // PERSONNALISATION DES ÉLÉMENTS DU MENU AVEC 2 OPTIONS.
        // OPTION 1:
        /*yield FormField::addPanel('Informations du produit')
            ->setIcon('fa fa-info-circle')
            ->setHelp('Renseignez les informations du produit')
            ->setCssClass('col-md-7')
            // On rajoute une classe CSS pour le panel
            ->setCustomOption('class', 'col-md-12')
            ->setCustomOption('style','margin-bottom: 0.5rem')
            ->setCustomOption('data-toggle', 'popover')
            ->setCustomOption('data-content', 'Renseignez les informations du produit')
            ->setColumns(3); // Permets de définir le nombre de colonnes pour les champs qui suivent.

        yield TextField::new('name', 'Nom du produit');
        yield SlugField::new('slug', 'Slug')->setTargetFieldName('name');
        yield ImageField::new('illustration', 'Illustration')
            ->setBasePath('uploads/')
            ->setUploadDir('public/uploads')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired(false);

        yield TextField::new('subtitle', 'Sous-titre');
        yield AssociationField::new('category', 'Catégorie(s)');

        yield FormField::addPanel('Contenu');
        yield TextEditorField::new('description', 'Description')
            ->setHelp('Renseignez la description du produit');
        yield MoneyField::new('price', 'Preuve')->setCurrency('EUR')->setStoredAsCents(true);
        yield BooleanField::new('isBest', 'Produit à la une');*/


        // OPTION 2:
        return [
            TextField::new('name', 'Nom du produit'),
            SlugField::new('slug', 'Slug')->setTargetFieldName('name'),
            ImageField::new('illustration', 'Illustration')
                ->setBasePath('uploads/')
                ->setUploadDir('public/uploads')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),
            TextField::new('subtitle', 'Sous-titre'),
            AssociationField::new('category', 'Catégories'),
            BooleanField::new('isBest', 'Produit à la une'),
            BooleanField::new('isStock', 'En stock'),
            MoneyField::new('price', 'Prix')->setCurrency('EUR')->setStoredAsCents(true),
            TextEditorField::new('description', 'Description'),
        ];
    }
}
