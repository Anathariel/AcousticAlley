<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use App\Entity\Categories;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

class ProductsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Products::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
            ->onlyOnIndex(),
            TextField::new('name'),
            NumberField::new('price'),
            NumberField::new('quantity'),
            TextEditorField::new('description'),
            ImageField::new('thumbnail')
            ->setBasePath('asset/media/products')
            ->setUploadDir('public/asset/media/products'),
            AssociationField::new('c_id')
                ->setRequired(true),
        ];
    }
}
