<?php

namespace Prolyfix\BankingBundle\Security\Voter;

use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use Prolyfix\BankingBundle\Entity\Entry;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BankingVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';


    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        if($subject instanceof EntityDto && $subject->getFqcn() == Entry::class)
        {
            return true;
        }
            return false;

    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if($user->hasRole('ROLE_ACCOUNTING'))
        {
            return true;
        }
        return false;
    }
}
