<?php

namespace App\Controller\Admin;

use App\Entity\Carousel;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CarouselCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Carousel::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Titre du carousel'),
            ImageField::new('illustration', 'Image du carousel')
                ->setBasePath('uploads/')
                ->setUploadDir('public/uploads')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),
            TextField::new('btnTitle', 'Titre du bouton'),
            TextField::new('bntUrl', 'Url du bouton'),
            TextareaField::new('description', 'Description du carousel'),
            BooleanField::new('isActivated', 'Activer le carousel'),
        ];
    }
}
