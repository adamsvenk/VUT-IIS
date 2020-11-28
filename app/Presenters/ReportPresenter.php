<?php

namespace App\Presenters;

use App\Model\HealthProblemService;
use App\Model\ReportService;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;

class ReportPresenter extends LoggedPresenter
{
    /**
     * @var ReportService
     * @inject
     */
    public $reportService;

    /**
     * @var HealthProblemService
     * @inject
     */
    public $healthProblemService;

    /**
     * @var int|null
     */
    public $healthProblemId;

    /**
     * @var int|null
     */
    public $reportId;

    public function beforeRender()
    {
        $this->template->healthProblemId = $this->healthProblemId;
    }

    public function actionList(int $healthProblemId)
    {
        $this->healthProblemId = $healthProblemId;

        $this->template->healthProblem = $this->healthProblemService->get($healthProblemId);
        $this->template->reports = $this->reportService->getAll($healthProblemId);
    }

    public function actionCreate(int $healthProblemId)
    {
        $this->healthProblemId = $healthProblemId;
    }

    public function actionEdit(int $healthProblemId, int $reportId)
    {
        $this->healthProblemId = $healthProblemId;
        $this->reportId = $reportId;

        $this['form']->setDefaults($this->reportService->getDefaults($reportId));
    }

    public function createComponentForm()
    {
        $form = new Form();

        $form->addHidden('healthProblemId');
        $form->addHidden('reportId');

        $form->addTextArea('text', 'Text')
            ->addRule(Form::MAX_LENGTH, 'Maximální délka textu je 1000 znaků.', 1000)
            ->setRequired('text je povinný');

        $form->addUpload('picture', 'Obrázek')
            ->setRequired(false);

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
            $this->reportService->update($this->healthProblemId, $this->reportId, $form->getValues(), $this->user->getId());

            if ($this->reportId !== null) {
                $this->flashMessage('Zdravotní zpráva byla upravena', 'success');
            } else {
                $this->flashMessage('Zdravotní zpráva byla vytvořena', 'success');
            }
        } catch (Exception $e) {
            if ($this->reportId !== null) {
                $this->flashMessage('Zdravotní zprávu se nepodařilo upravit', 'danger');
            } else {
                $this->flashMessage('Zdravotní zprávu se nepodařilo vytvořit', 'danger');
            }
        }

        $this->redirect('Report:list', ['healthProblemId' => $this->healthProblemId]);
    }

    /**
     * @param int $healthProblemId
     * @param int $reportId
     * @throws AbortException
     */
    public function actionDelete(int $healthProblemId, int $reportId)
    {
        try {
            $this->reportService->delete($reportId);

            $this->flashMessage('Zdravotní zpráva byla smazána', 'success');
        } catch (Exception $e) {
            $this->flashMessage('Zdravotní zprávu se nepodařilo vymazat', 'danger');
        }

        $this->redirect('Report:list', ['healthProblemId' => $healthProblemId]);
    }

    public function actionDetail(int $healthProblemId, int $reportId)
    {
        $this->healthProblemId = $healthProblemId;
        $this->template->report = $this->reportService->get($reportId);
    }

    public function actionImage(int $reportId)
    {
        $report = $this->reportService->get($reportId);

        header('Content-type: image/png');
        echo $report->Picture;
    }
}