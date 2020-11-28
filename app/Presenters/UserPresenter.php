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

        $this->allowedRoles([UserManager::ROLE_ADMIN]);
    }

    public function renderList(): void
    {
        $this->template->users = $this->userService->getAll();
    }

    public function createComponentCreateForm()
    {
        $form = new Form();

        $form->addText('username', 'Uživatelské jméno')
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

    /**
     * @param Form $form
     * @throws AbortException
     */
    public function formSuccess(Form $form)
    {
        try {
            $this->userService->update($this->userId, $form->getValues());

            if ($this->userId !== null) {
                $this->flashMessage('Uživatel byl upraven', 'success');
            } else {
                $this->flashMessage('Uživatel byl vytvořen', 'success');
            }

        } catch (Exception $e) {
            if ($this->userId !== null) {
                $this->flashMessage('Uživatele se nepodařilo vytvořit', 'danger');
            } else {
                $this->flashMessage('Uživatele se nepodařilo upravit', 'danger');
            }
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

            $this->flashMessage('Uživatel byl smazán', 'success');
        } catch (Exception $e) {
            $this->flashMessage('Uživatele se nepodařilo vymazat', 'danger');
        }

        $this->redirect('User:list');
    }
}