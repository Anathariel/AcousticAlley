<?php

namespace App\Controller\Admin;

use App\Entity\Users;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class UsersCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Users::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
            ->onlyOnIndex(),
            yield EmailField::new('email'),
            yield TextField::new('firstname'),
            yield TextField::new('lastname'),
            yield TextField::new('adress'),
            yield TextField::new('phone'),
            yield DateField::new('birthday'),
            yield BooleanField::new('isVerified')
            ->renderAsSwitch(false),
            $roles = ['ROLE_ADMIN', 'ROLE_MODERATOR', 'ROLE_USER'],
            yield ChoiceField::new('roles')
            ->setChoices(array_combine($roles, $roles))
            ->allowMultipleChoices()
            ->renderExpanded()
            ->renderAsBadges(),
        ];
    }
}
