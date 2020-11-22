<?php

namespace App\Presenters;

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


    public function renderList(): void
    {
        $this->template->users = $this->userService->getAll();
    }


    public function createComponentCreateForm()
    {
        $form = new Form();

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

        $form->onSuccess[] = [$this, 'formSubmit'];

        $form->addSubmit('submit');

        return $form;
    }


    public function formSubmit(Form $form)
    {
        $this->userService->create($form->getValues());
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