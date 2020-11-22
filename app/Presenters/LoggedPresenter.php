<?php

declare(strict_types=1);

namespace App\Presenters;

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
}
