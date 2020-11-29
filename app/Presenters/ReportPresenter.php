<?php

namespace App\Presenters;

use App\Model\HealthProblemService;
use App\Model\ReportService;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use stdClass;

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
    public $examinationId;

    /**
     * @var int|null
     */
    public $reportId;
    
    public $DateTime;

    public function beforeRender()
    {
        $this->template->examinationId = $this->examinationId;
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

    public function actionCreateExamination(int $examinationId)
    {
        $this->examinationId = $examinationId;
    }

    public function actionEditExamination(int $examinationId, int $reportId)
    {
        $this->examinationId = $examinationId;
        $this->reportId = $reportId;

        $this['form']->setDefaults($this->reportService->getDefaults($reportId));
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

        $form->addText('subject', 'Subject')
            ->addRule(Form::MAX_LENGTH, 'Maximální délka textu je 50 znaků.', 50)
            ->setRequired('předmět je povinný');
        
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
        $this->reportService->update($this->healthProblemId, $this->examinationId, $this->reportId, $form->getValues(), $this->user->getId());
        try {

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

        if ($this->examinationId !== null) {
            $this->redirect('Examination:detail', ['examinationId' => $this->examinationId]);
        } else {
            $this->redirect('Report:list', ['healthProblemId' => $this->healthProblemId]);
        }
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

    /**
     * @param int $examinationId
     * @param int $reportId
     * @throws AbortException
     */
    public function actionDeleteExamination(int $examinationId, int $reportId)
    {
        try {
            $this->reportService->delete($reportId);

            $this->flashMessage('Zdravotní zpráva byla smazána', 'success');
        } catch (Exception $e) {
            $this->flashMessage('Zdravotní zprávu se nepodařilo vymazat', 'danger');
        }

        $this->redirect('Examination:detail', ['examinationId' => $examinationId]);
    }

    public function actionDetail(int $healthProblemId, int $reportId)
    {
        $this->healthProblemId = $healthProblemId;
        $this->template->report = $this->reportService->get($reportId);
    }

    public function actionDetailExamination(int $examinationId, int $reportId)
    {
        $this->examinationId = $examinationId;
        $this->template->report = $this->reportService->get($reportId);
    }

    public function actionImage(int $reportId)
    {
        /** @var stdClass $report */
        $report = $this->reportService->get($reportId);

        header('Content-type: image/png');
        echo $report->Picture;
    }
    
}
