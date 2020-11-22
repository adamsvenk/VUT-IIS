<?php

namespace App\Presenters;

use App\Model\UserService;
use Exception;
use Nette\Application\AbortException;

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