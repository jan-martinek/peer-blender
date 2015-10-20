<?php

namespace App\Presenters;

use Nette;
use DateTime;
use App\Forms\SignFormFactory;
use Nette\Application\UI\Form;
use Nette\Security\Passwords;

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
            if ($this->user->id) {
                $this->logEvent($this->userRepository->find($this->user->id), 'login');
            }
            if (!empty($this->backlink)) {
                try {
                    $this->getPresenter()->redirect($this->getPresenter()->restoreRequest($this->backlink));    
                } catch (Nette\Application\UI\InvalidLinkException $e) {
                    $form->getPresenter()->redirect('Homepage:');
                }
                
                
            } else {
                $form->getPresenter()->redirect('Homepage:');
            }

        };

        return $form;
    }
    
    public function actionIn($backlink = null) 
    {
        if ($this->user->isLoggedIn()) {
            $this->redirect('Homepage:default');
        }   
    }
    
    
    public function actionOut()
    {
        $this->logEvent($this->userInfo, 'logout');
        $this->getUser()->logout();
        $this->flashMessage('You have been signed out.');
        $this->redirect('in');
    }
    
}
