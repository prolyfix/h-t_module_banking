<?php

namespace Prolyfix\BankingBundle;

use App\Entity\Module\ModuleRight;
use Prolyfix\BankingBundle\Entity\AccountType;
use App\Module\ModuleBundle;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Security\AuthorizationChecker;
use Prolyfix\BankingBundle\Entity\Account;
use Prolyfix\BankingBundle\Entity\Entry;
use Prolyfix\ChecklistBundle\Entity\Checklist;
use Prolyfix\CrmBundle\Entity\Appointment;
use Prolyfix\CrmBundle\Entity\Contact;
use Prolyfix\CrmBundle\Entity\ThirdParty;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ProlyfixBankingBundle extends ModuleBundle
{
    private $authorizationChecker;

    public function setAuthorizationChecker(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public static function getTables(): array
    {
        return [
            Account::class,
            Entry::class
        ];
    }

    const IS_MODULE = true;
    public static function getShortName(): string
    {
        return 'BankingBundle';
    }
    public static function getModuleName(): string
    {
        return 'Banking';
    }
    public static function getModuleDescription(): string
    {
        return 'Banking Module';
    }
    public static function getModuleType(): string
    {
        return 'module';
    }
    public static function getModuleConfiguration(): array
    {
        return [];
    }

    public static function getModuleRights(): array
    {
        return [];
    }

    public function getMenuConfiguration(): array
    {
        $authorizationChecker = $this->authorizationChecker;
        if (!$authorizationChecker->isGranted('ROLE_ACCOUNTING')) {
            return [];
        }
        return ['banking' => [
            MenuItem::linkToCrud('Banking List', 'fas fa-list', Account::class),
            MenuItem::linkToCrud('Entry List', 'fas fa-list', Entry::class),
            MenuItem::linkToCrud('accountType', 'fas fa-list', AccountType::class)
        ]];
    }

    public static function getUserConfiguration(): array
    {
        return [];
    }

    public static function getModuleAccess(): array
    {
        return [];
    }


}