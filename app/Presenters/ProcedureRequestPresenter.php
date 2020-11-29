<?php

namespace App\Presenters;

use App\Model\ProcedureRequestService;
use App\Model\ProcedureService;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;

class ProcedureRequestPresenter extends LoggedPresenter
{
    /**
     * @var int|null
     */
    public $examinationId;

    /**
     * @var ProcedureRequestService
     * @inject
     */
    public $procedureRequestService;

    /**
     * @var ProcedureService
     * @inject
     */
    public $procedureService;


    public function actionCreate(int $examinationId)
    {
        $this->examinationId = $examinationId;
        $this->template->examinationId = $examinationId;
    }

    public function createComponentForm()
    {
        $form = new Form();

        $form->addSelect('procedure', 'Úkon')
            ->setItems($this->procedureService->getList())
            ->setRequired('Úkon je povinný');

        $form->addSubmit('submit');

        $form->onSuccess[] = [$this, 'formSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     * @throws AbortException
     */
    public function formSuccess(Form $form)
    {
        try {
            $this->procedureRequestService->create($this->examinationId, $this->user->getId(), $form->getValues());

            $this->flashMessage('Žádost o proplacení úkonu byla vložena', 'success');
        } catch (Exception $e) {
            $this->flashMessage('Žádost o proplacení úkonu se nepodařilo vložit', 'danger');
        }

        $this->redirect('Examination:detail', ['examinationId' => $this->examinationId]);
    }

    /**
     * @param int $examinationId
     * @param int $procedureRequestId
     * @throws AbortException
     */
    public function actionDelete(int $examinationId, int $procedureRequestId): void
    {
        try {
            $this->procedureRequestService->delete($procedureRequestId);

            $this->flashMessage('Žádost u uhrazení úkonu byla smazána', 'success');
        } catch (Exception $e) {
            $this->flashMessage('Žádost u uhrazení úkonu se nepodařilo vymazat', 'danger');
        }

        $this->redirect('Examination:detail', ['examinationId' => $examinationId]);
    }
}