<?php

namespace Prolyfix\BankingBundle;

use App\Entity\Module\ModuleRight;
use Prolyfix\BankingBundle\Entity\AccountType;
use App\Module\ModuleBundle;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Security\AuthorizationChecker;
use Prolyfix\BankingBundle\Controller\Admin\EntryCrudController;
use Prolyfix\BankingBundle\Entity\Account;
use Prolyfix\BankingBundle\Entity\Entry;
use Prolyfix\ChecklistBundle\Entity\Checklist;
use Prolyfix\CrmBundle\Entity\Appointment;
use Prolyfix\CrmBundle\Entity\Contact;
use Prolyfix\CrmBundle\Entity\ThirdParty;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ProlyfixBankingBundle extends ModuleBundle
{
    private $authorizationChecker;

    public function setAuthorizationChecker(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        dump($config);
        $container->import('../config/services.yaml');
            if (class_exists(EntryCrudController::class)) { // Replace with one of your actual controller class names
        dump('MyBankingController class exists.');
    } else {
        dump('MyBankingController class does NOT exist at that FQCN.');
    }

    // Try to check if a service from your bundle is registered (e.g., a controller)
    // This will only work if the service definition itself is already processed
    // For auto-configured services, the FQCN is usually the service ID.
    if ($builder->hasDefinition(EntryCrudController::class)) {
         dump('MyBankingController service definition exists in container.');
    } else {
         dump('MyBankingController service definition NOT found in container yet.');
    }

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