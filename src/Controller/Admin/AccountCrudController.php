<?php

namespace Prolyfix\BankingBundle\Controller\Admin;

use Prolyfix\HolidayAndTime\Controller\Admin\BaseCrudController;
use Prolyfix\BankingBundle\Entity\Account;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AccountCrudController extends BaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return Account::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
        ];
    }
    
}
