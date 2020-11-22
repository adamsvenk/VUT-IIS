<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\UserManager;
use Nette;


/**
 * Base presenter for all application presenters.
 */
abstract class LoggedPresenter extends Nette\Application\UI\Presenter
{
    /**
     * @throws Nette\Application\AbortException
     */
    public function startup()
    {
        parent::startup();

        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
    }


    public function beforeRender()
    {
        parent::beforeRender();

        $user = $this->getUser();

        $this->template->isAdmin = $user->isInRole(UserManager::ROLE_ADMIN);
        $this->template->isInsuranceWorker = $user->isInRole(UserManager::ROLE_INSURANCE_WORKER);
        $this->template->isDoctor = $user->isInRole(UserManager::ROLE_DOCTOR);
        $this->template->isPatient = $user->isInRole(UserManager::ROLE_PATIENT);
    }
}
