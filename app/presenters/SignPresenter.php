<?php

namespace App\Presenters;

use Nette;
use App\Forms\SignFormFactory;

/**
 * Sign in/out presenters.
 */
class SignPresenter extends BasePresenter
{
    /** @var SignFormFactory @inject */
    public $factory;

    /**
     * Sign-in form factory.
     *
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSignInForm()
    {
        $form = $this->factory->create();
        $form->onSuccess[] = function ($form) {
            $this->logEvent($this->userEntity, 'login');
            $form->getPresenter()->redirect('Homepage:');
        };

        return $form;
    }

    public function actionOut()
    {
        $this->getUser()->logout();
        $this->logEvent($this->userEntity, 'logout');
        $this->flashMessage('You have been signed out.');
        $this->redirect('in');
    }
}
