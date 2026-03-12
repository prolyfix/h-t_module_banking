<?php

namespace Prolyfix\BankingBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Prolyfix\BankingBundle\Entity\Entry;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Prolyfix\BankingBundle\Form\ImporterTypeForm;
use Prolyfix\BankingBundle\Importer\BankImporterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EntryCrudController extends AbstractCrudController
{
    /**
     * @param iterable<BankImporterInterface> $importers All tagged bank importers
     */
    public function __construct(private iterable $importers = [])
    {
    }

    public static function getEntityFqcn(): string
    {
        return Entry::class;
    }

    /**
     * Resolves an importer by its name as submitted via the form.
     */
    private function resolveImporter(string $name): ?BankImporterInterface
    {
        foreach ($this->importers as $importer) {
            if ($importer->getName() === $name) {
                return $importer;
            }
        }
        return null;
    }

    public function import(Request $request): Response
    {
        $form = $this->createForm(ImporterTypeForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $importerName = $form->get('typeOfImport')->getData();
            $bank         = $form->get('bankAccount')->getData();
            $importer     = $this->resolveImporter($importerName);

            if ($importer === null) {
                return new JsonResponse([
                    'status'  => 'error',
                    'message' => 'Unknown importer: ' . $importerName,
                ]);
            }

            if ($importer->getImportMode() === BankImporterInterface::IMPORT_MODE_FILE) {
                $file = $request->files->get('importer_type_form')['media'] ?? null;

                if (!$file) {
                    return new JsonResponse([
                        'status'  => 'error',
                        'message' => 'No file uploaded',
                    ]);
                }

                if (!$importer->isFormatAllowed($file->getClientOriginalExtension())) {
                    return new JsonResponse([
                        'status'  => 'error',
                        'message' => 'File format not allowed',
                    ]);
                }

                if (!$importer->isFileRight($file)) {
                    return new JsonResponse([
                        'status'  => 'error',
                        'message' => 'File is not valid',
                    ]);
                }

                $importer->import($file, $bank, true);
            } else {
                // API-based importer: no file is required – trigger the import directly.
                $importer->import(null, $bank, true);
            }
        }

        return $this->render('common/simpleForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function configureActions(Actions $actions): Actions
    {
        $viewYear = Action::new('import', 'import', 'fa fa-calendar')
            ->linkToCrudAction('import')
            ->createAsGlobalAction()
            ->setHtmlAttributes([
                'data-action' => 'click->modal-form#openModal',
            ]);
        return $actions->add(Crud::PAGE_INDEX, $viewYear);
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            DateField::new('date'),
            AssociationField::new('bank'),
            TextField::new('counterpart'),
            TextField::new('title'),
            NumberField::new('amount'),
            CollectionField::new('media')
                ->setTemplatePath('admin/field/medias.html.twig')->hideOnForm()
        ];
    }
    
}
