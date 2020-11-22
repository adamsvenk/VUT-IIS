<?php

namespace App\Presenters;

use App\Model\UserService;

class UserPresenter extends LoggedPresenter
{
    /**
     * @var UserService
     * @inject
     */
    public $userService;


    public function renderList()
    {
        $this->template->users = $this->userService->getAll();
    }
}