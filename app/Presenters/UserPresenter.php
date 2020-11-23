<?php

namespace App\Presenters;

use App\Model\UserManager;
use App\Model\UserService;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;

class UserPresenter extends LoggedPresenter
{
    /**
     * @var UserService
     * @inject
     */
    public $userService;

    public function startup()
    {
        parent::startup();

        if (!$this->user->isInRole(UserManager::ROLE_ADMIN)) {
            $this->flashMessage('Nedostatečná práva', 'warning');
            $this->redirect('Homepage:Default');
        }
    }

    public function renderList(): void
    {
        $this->template->users = $this->userService->getAll();
    }


    public function createComponentCreateForm()
    {
        $form = new Form();

        $userId = $this->getRequest()->getParameter('userId') ? (int) $this->getRequest()->getParameter('userId') : null;

        $form->addHidden('userId', $userId);

        $form->addText('username', 'Uživ jméno')
            ->addRule(Form::MAX_LENGTH, 'Maximální délka uživatelského jména je 45 znaků.', 45)
            ->setRequired();

        $form->addPassword('password', 'Heslo')
            ->setRequired();

        $form->addSelect('role', 'Role')
            ->setItems(UserService::roles())
            ->setRequired(true);

        $form->addText('fullName', 'Celé jméno')
            ->addRule(Form::MAX_LENGTH, 'Maximální délka jména je 255 znaků', 255);

        $form->addText('date', 'Datum narození')
            ->setHtmlType('date');

        $form->addText('function', 'Funkce')
            ->addRule(Form::MAX_LENGTH, 'Maximální délka funkce je 45 znaků', 45);

        //edit for
        if ($form['userId']->getValue() !== "") {
            $form['password']->setRequired(false);

            $form->setDefaults($this->userService->getDefaults($userId));

            $form->onSuccess[] = [$this, 'formEditSubmit'];
        } else {
            $form->onSuccess[] = [$this, 'formCreateSubmit'];
        }

        $form->addSubmit('submit');

        return $form;
    }


    public function formCreateSubmit(Form $form)
    {
        $this->userService->create($form->getValues());
        try {

            $this->flashMessage('Uživatel byl vytvořen');
        } catch (Exception $e) {
            $this->flashMessage('Uživatele se nepodařilo vytvořit', 'danger');
        }

        $this->redirect('User:list');
    }


    public function formEditSubmit(Form $form)
    {
        $this->userService->edit($form->getValues());
        try {

            $this->flashMessage('Uživatel byl upraven');
        } catch (Exception $e) {
            $this->flashMessage('Uživatele se nepodařilo upravit', 'danger');
        }

        $this->redirect('User:list');
    }


    /**
     * @param int $userId
     * @throws AbortException
     */
    public function actionDelete(int $userId): void
    {
        try {
            $this->userService->delete($userId);

            $this->flashMessage('Uživatel byl smazán');
        } catch (Exception $e) {
            $this->flashMessage('Uživatele se nepodařilo vymazat', 'danger');
        }

        $this->redirect('User:list');
    }
}