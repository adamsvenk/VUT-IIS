<?php

namespace App\Presenters;

use App\Model\ProcedureService;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;

class ProcedurePresenter extends LoggedPresenter
{
    /**
     * @var ProcedureService
     * @inject
     */
    public $procedureService;

    /** @var int|null */
    public $procedureId;

    /** @var int|null */
    public $examinationId;

    public function renderList(): void
    {
        $this->template->procedures = $this->procedureService->getAll();
    }

    public function createComponentForm()
    {
        $form = new Form();

        $form->addText('name', 'Jméno úkonu')
            ->addRule(Form::MAX_LENGTH, 'Maximální délka procedury je 255 znaků.', 255)
            ->setRequired('Jméno procedury je povinné');

        $form->addInteger('price', 'Cena')
            ->setRequired('Cena procedury je povinná');

        $form->addSubmit('submit');

        $form->onSuccess[] = [$this, 'formSuccess'];

        return $form;
    }

    public function actionEdit(int $procedureId)
    {
        $this->procedureId = $procedureId;

        /** @var Form $form */
        $form = $this['form'];

        $form->setDefaults($this->procedureService->getDefaults($procedureId));
    }

    /**
     * @param Form $form
     * @throws AbortException
     */
    public function formSuccess(Form $form)
    {
        $this->procedureService->update($this->procedureId, $form->getValues());
        try {

            if ($this->procedureId !== null) {
                $this->flashMessage('Hrazený úkon byl upraven', 'success');
            } else {
                $this->flashMessage('Hrazený úkon byl vytvořen', 'success');
            }

        } catch (Exception $e) {
            if ($this->procedureId !== null) {
                $this->flashMessage('Hrazený úkon se nepodařilo vytvořit', 'danger');
            } else {
                $this->flashMessage('Hrazený úkon se nepodařilo upravit', 'danger');
            }
        }

        $this->redirect('Procedure:list');
    }

    /**
     * @param int $procedureId
     * @throws AbortException
     */
    public function actionDelete(int $procedureId): void
    {
        try {
            $this->procedureService->delete($procedureId);

            $this->flashMessage('Hrazený úkon byl smazán', 'success');
        } catch (Exception $e) {
            $this->flashMessage('Hrazený úkon se nepodařilo vymazat', 'danger');
        }

        $this->redirect('Procedure:list');
    }
}