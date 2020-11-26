<?php

namespace App\Presenters;

use App\Model\HealthProblemService;
use App\Model\UserService;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;

class HealthProblemPresenter extends LoggedPresenter
{
    /**
     * @var HealthProblemService
     * @inject
     */
    public $healthProblemService;

    /**
     * @var UserService
     * @inject
     */
    public $userService;

    /** @var int|null */
    public $healthProblemId;

    public function renderList(): void
    {
        $this->template->healthProblems = $this->healthProblemService->getAll();
    }

    public function createComponentForm()
    {
        $form = new Form();

        $form->addSelect('patient', 'Pacient')
            ->setItems($this->userService->getPatientList());

        $form->addText('name', 'Jméno')
            ->addRule(Form::MAX_LENGTH, 'Maximální délka jména je 100 znaků.', 100)
            ->setRequired('Jméno problému je povinné');

        $form->addText('description', 'Popis')
            ->addRule(Form::MAX_LENGTH, 'Maximální délka popisu je 300 znaků.', 100)
            ->setRequired(false);

        $form->addSelect('state', 'Stav')
            ->setItems(HealthProblemService::getStateList());

        $form->addSubmit('submit');

        $form->onSuccess[] = [$this, 'formSuccess'];

        return $form;
    }

    public function actionEdit(int $healthProblemId)
    {
        $this->healthProblemId = $healthProblemId;

        /** @var Form $form */
        $form = $this['form'];

        $form->setDefaults($this->healthProblemService->getDefaults($healthProblemId));
    }

    /**
     * @param Form $form
     * @throws AbortException
     */
    public function formSuccess(Form $form)
    {
        try {
            $doctorId = $this->user->getId();
            $this->healthProblemService->update($this->healthProblemId, $form->getValues(), $doctorId);

            if ($this->healthProblemId !== null) {
                $this->flashMessage('Zdravotní problém byl upraven', 'success');
            } else {
                $this->flashMessage('Zdravotní problém byl vytvořen', 'success');
            }
        } catch (Exception $e) {
            if ($this->healthProblemId !== null) {
                $this->flashMessage('Zdravotní problém se nepodařilo upravit', 'danger');
            } else {
                $this->flashMessage('Zdravotní problém se nepodařilo vytvořit', 'danger');
            }
        }

        $this->redirect('HealthProblem:list');
    }

    /**
     * @param int $healthProblemId
     * @throws AbortException
     */
    public function actionDelete(int $healthProblemId): void
    {
        try {
            $this->healthProblemService->delete($healthProblemId);

            $this->flashMessage('Zdravotní problém byl smazán', 'success');
        } catch (Exception $e) {
            $this->flashMessage('Zdravotní problém se nepodařilo vymazat', 'danger');
        }

        $this->redirect('HealthProblem:list');
    }
}