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
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use PHPUnit\Util\Json;
use Prolyfix\BankingBundle\Form\ImporterTypeForm;
use Prolyfix\BankingBundle\Importer\ApobankXlsImporter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EntryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Entry::class;
    }
    public function import(Request $request, ApobankXlsImporter $importer):Response
    {
        $form = $this->createForm(ImporterTypeForm::class);
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid())
        {
            $file = $request->files->get('importer_type_form')['media'];
            $bank = $form->get('bankAccount')->getData();
            if(!$file)
            {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'No file uploaded'
                ]);
            }
            if(!$importer->isFormatAllowed($file)){
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'File format not allowed'
                ]);
            }
            if(!$importer->isFileRight($file)){
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'File is not right'
                ]);
            }
            $imported = $importer->import($file,$bank, true);

        }
        return $this->render('common/simpleForm.html.twig', [
            'form' => $form->createView()
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
