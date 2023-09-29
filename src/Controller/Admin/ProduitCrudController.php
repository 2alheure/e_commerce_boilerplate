<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

class ProduitCrudController extends AbstractCrudController {
    public static function getEntityFqcn(): string {
        return Produit::class;
    }

    public function configureFields(string $pageName): iterable {
        return [
            TextField::new('nom'),
            MoneyField::new('prix')->setCurrency('EUR'),
            TextEditorField::new('description'),
            ImageField::new('image')
                ->setBasePath('assets/uploads/products')
                ->setUploadDir('public/assets/uploads/products')
                ->setUploadedFileNamePattern('[uuid].[extension]'),
            IntegerField::new('stock'),
        ];
    }
}
