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

    /** @persistent */
    public $backlink;
    
    /** @var \Nette\Http\Request @inject */
    public $request;    

    /**
     * Sign-in form factory.
     *
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSignInForm()
    {
        $form = $this->factory->create();
        $form->onSuccess[] = function ($form) {
            $this->logEvent($this->userRepository->find($this->user->id), 'login');
            if (!empty($this->backlink)) {
                $this->getPresenter()->redirect($this->getPresenter()->restoreRequest($this->backlink));
            } else {
                $form->getPresenter()->redirect('Homepage:');
            }

        };

        return $form;
    }
    
    public function actionIn($backlink = null) {
        
    }

    public function actionOut()
    {
        $this->logEvent($this->userEntity, 'logout');
        $this->getUser()->logout();
        $this->flashMessage('You have been signed out.');
        $this->redirect('in');
    }
}
