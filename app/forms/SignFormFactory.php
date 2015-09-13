<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;

class SignFormFactory extends Nette\Object
{
    /** @var User */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return Form
     */
    public function create()
    {
        $form = new Form();
        $form->addText('username', 'Username:')
            ->setRequired('Please enter your username.');

        $form->addPassword('password', 'Password:')
            ->setRequired('Please enter your password.');

        $form->addCheckbox('remember', 'Keep me signed in');

        $form->addSubmit('send', 'Sign in');

        $form->onSuccess[] = array($this, 'formSucceeded');

        return $form;
    }

    public function formSucceeded($form, $values)
    {
        if ($values->remember) {
            $this->user->setExpiration('14 days', false);
        } else {
            $this->user->setExpiration('90 minutes', true);
        }

        try {
            $this->user->login($values->username, $values->password);
        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }
}
