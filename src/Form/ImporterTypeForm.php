<?php

namespace Prolyfix\BankingBundle\Form;

use Prolyfix\BankingBundle\Entity\Account;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImporterTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('typeOfImport',ChoiceType::class,[
                'choices'=>[
                    'apobank_xls' =>  'aaa'
                ]
            ])
            ->add('bankAccount',EntityType::class,[
                'class' => Account::class,
                'choice_label' => 'name',
                'placeholder' => 'Select a bank account'
            ])
            ->add('media',FileType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
