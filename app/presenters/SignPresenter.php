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
            $this->logEvent($this->userRepository->find($this->user->id), 'login');
            if (!empty($this->backlink)) {
                $this->getPresenter()->redirect($this->getPresenter()->restoreRequest($this->backlink));
            } else {
                $form->getPresenter()->redirect('Homepage:');
            }

        };

        return $form;
    }
    
    public function actionIn($backlink = null) 
    {
        
    }
    
    
    public function actionOut()
    {
        $this->logEvent($this->userEntity, 'logout');
        $this->getUser()->logout();
        $this->flashMessage('You have been signed out.');
        $this->redirect('in');
    }
    
    public function actionPasswordReset() 
    {
        
    }
    
    protected function createComponentPasswordResetForm() 
    {
        $form = new Form;

        $emailLabel = $this->translator->translate('messages.app.emailAddress');
        $form->addText('email', $emailLabel);
        
        $submitLabel = $this->translator->translate('messages.app.requestNewPassword');
        $form->addSubmit('submit', $submitLabel);
        
        $form->onSuccess[] = array($this, 'passwordResetFormSucceeded');
        
        return $form;
    }
    
    public function passwordResetFormSucceeded(Form $form, $values) 
    {
        if ($user = $this->userRepository->findByEmail($values->email)) {
            $user->initiatePasswordReset();
            $this->userRepository->persist($user);
            $user->sendPasswordResetEmail($this);
            $this->flashMessage($this->translator->translate('messages.app.passwordResetRequestSent', NULL, array('address' => $values->email)));
            $this->redirect('this');
        } else {
            $this->flashMessage($this->translator->translate('messages.app.accountNotFoundByEmail', NULL, array('address' => $values->email)));
            $this->redirect('this');
        }  
        return;
    }
    
    public function actionNewPassword($email, $token)
    {
        if ($user = $this->userRepository->findByEmail($email)) {
            if (!$user->hasPasswordResetBeenInitiated($token)) {
                $this->flashMessage($this->translator->translate('messages.app.passwordResetRequestNotInitiated', NULL, array('address' => $email)));
                $this->redirect('Sign:passwordReset');
            }
        } else {
            $this->flashMessage($this->translator->translate('messages.app.accountNotFoundByEmail', NULL, array('address' => $email)));
            $this->redirect('Sign:passwordReset');
        }
    }
    
    protected function createComponentNewPasswordForm() 
    {
        $form = new Form;

        $passwordLabel = $this->translator->translate('messages.app.newPassword');
        $form->addPassword('password', $passwordLabel);
        
        $submitLabel = $this->translator->translate('messages.app.setNewPassword');
        $form->addSubmit('submit', $submitLabel);
        
        $form->onSuccess[] = array($this, 'newPasswordFormSucceeded');
        
        return $form;
    }
    
    public function newPasswordFormSucceeded(Form $form, $values) 
    {
        $user = $this->userRepository->find($this->user->id);
        $user->password = Passwords::hash($values->password);
        $this->userRepository->persist($user);
        $this->flashMessage($this->translator->translate('messages.app.newPasswordSet'));
        $this->user->logout();
        $this->redirect('Sign:in');
        return;
    }
}
