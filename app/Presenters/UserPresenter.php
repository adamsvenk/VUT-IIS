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

    /** @var int|null */
    public $userId;

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

        $form->addText('username', 'Uživ jméno')
            ->addRule(Form::MAX_LENGTH, 'Maximální délka uživatelského jména je 45 znaků.', 45)
            ->setRequired('Pole uživatelské jméno je povinné');

        $form->addPassword('password', 'Heslo')
            ->setRequired('Pole heslo je povinne');

        $form->addSelect('role', 'Role')
            ->setItems(UserService::roles())
            ->setRequired('Výběr role je povinný');

        $form->addText('fullName', 'Celé jméno')
            ->addRule(Form::MAX_LENGTH, 'Maximální délka jména je 255 znaků', 255);

        $form->addText('date', 'Datum narození')
            ->setHtmlType('date');

        $form->addText('function', 'Funkce')
            ->addRule(Form::MAX_LENGTH, 'Maximální délka funkce je 45 znaků', 45);

        $form->addSubmit('submit');

        $form->onSuccess[] = [$this, 'formSuccess'];

        return $form;
    }

    public function actionEdit(int $userId)
    {
        $this->userId = $userId;

        /** @var Form $form */
        $form = $this['createForm'];

        $form['password']->setRequired(false);
        $form->setDefaults($this->userService->getDefaults($userId));
    }

    public function formSuccess(Form $form)
    {
        $this->userService->update($this->userId, $form->getValues());
        try {

            $this->flashMessage('Uživatel byl vytvořen');
        } catch (Exception $e) {
            $this->flashMessage('Uživatele se nepodařilo vytvořit', 'danger');
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