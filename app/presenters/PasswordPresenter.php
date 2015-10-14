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
class PasswordPresenter extends BasePresenter
{
    /** @var SignFormFactory @inject */
    public $factory;

    /** @persistent */
    public $backlink;
    
    public function actionReset() 
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
    
    public function actionNew($email, $token)
    {
        if ($user = $this->userRepository->findByEmail($email)) {
            if (!$user->hasPasswordResetBeenInitiated()) {
                $this->flashMessage($this->translator->translate('messages.app.passwordResetRequestNotInitiated', NULL, array('address' => $email)));
                $this->redirect('Password:reset');
            } elseif (!$user->isPasswordResetTokenValid($token)) {
                $this->flashMessage($this->translator->translate('messages.app.passwordResetTokenInvalid', NULL, array('address' => $email)));
                $this->redirect('Password:reset');
            } else {
                $this->template->token = $token;
                $this->template->email = $email;
            }
        } else {
            $this->flashMessage($this->translator->translate('messages.app.accountNotFoundByEmail', NULL, array('address' => $email)));
            $this->redirect('Password:reset');
        }
    }
    
    protected function createComponentNewPasswordForm() 
    {
        $form = new Form;

        $passwordLabel = $this->translator->translate('messages.app.newPassword');
        $form->addPassword('password', $passwordLabel);
        
        $form->addHidden('email');
        $form->addHidden('token');
        
        $submitLabel = $this->translator->translate('messages.app.setNewPassword');
        $form->addSubmit('submit', $submitLabel);
        
        $form->onSuccess[] = array($this, 'newPasswordFormSucceeded');
        
        return $form;
    }
    
    public function newPasswordFormSucceeded(Form $form, $values) 
    {
        $user = $this->userRepository->findByEmail($values->email);
        if (!$user->hasPasswordResetBeenInitiated()) {
            $this->flashMessage($this->translator->translate(
                'messages.app.passwordResetRequestNotInitiated', 
                NULL, 
                array('address' => $values->email)
            ));
            $this->redirect('Password:reset');
        }
        if (!$user->isPasswordResetTokenValid($values->token)) {
            $this->flashMessage($this->translator->translate(
                'messages.app.passwordResetTokenInvalid', 
                NULL, 
                array('address' => $values->email)
            ));
            $this->redirect('Password:reset');
        } 
        $user->password = Passwords::hash($values->password);
        $user->passwordResetValidUntil = null;
        $user->passwordResetToken = null;
        $this->userRepository->persist($user);
        $this->flashMessage($this->translator->translate('messages.app.newPasswordSet'));
        $this->redirect('Homepage:default');
        return;
    }
}
