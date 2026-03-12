<?php

namespace Prolyfix\BankingBundle\Form;

use Prolyfix\BankingBundle\Entity\Account;
use Prolyfix\BankingBundle\Importer\BankImporterInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImporterTypeForm extends AbstractType
{
    /**
     * @param iterable<BankImporterInterface> $importers All tagged bank importers
     */
    public function __construct(private iterable $importers = [])
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Build choices from all registered importers: label => value
        $choices = [];
        foreach ($this->importers as $importer) {
            $choices[$importer->getName()] = $importer->getName();
        }

        // Fall back to the hard-coded default so the form always works even when
        // the DI tag has not been set up yet (e.g. in a fresh install).
        if (empty($choices)) {
            $choices = ['apobank_xls' => 'apobank_xls'];
        }

        // Collect all file-mode importer names for the front-end toggle.
        $fileImporterNames = [];
        foreach ($this->importers as $importer) {
            if ($importer->getImportMode() === BankImporterInterface::IMPORT_MODE_FILE) {
                $fileImporterNames[] = $importer->getName();
            }
        }

        $builder
            ->add('typeOfImport', ChoiceType::class, [
                'choices' => $choices,
                'label'   => 'Import type',
                'attr'    => [
                    'data-file-importers' => implode(',', $fileImporterNames),
                ],
            ])
            ->add('bankAccount', EntityType::class, [
                'class'        => Account::class,
                'choice_label' => 'name',
                'placeholder'  => 'Select a bank account',
            ]);

        // The file field is added by default; its visibility is toggled in the
        // browser via the data-file-importers attribute on the select above.
        // For API-mode importers the field is still part of the form but marked
        // optional so the form can be submitted without a file.
        $builder->add('media', FileType::class, [
            'required' => false,
            'label'    => 'File (for file-based importers)',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
