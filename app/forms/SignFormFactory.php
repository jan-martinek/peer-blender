<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;

class SignFormFactory extends Nette\Object
{
    /** @var User */
    private $user;
    
    /** @var \Kdyby\Translation\Translator @inject */
    public $translator;
    

    public function __construct(User $user, \Kdyby\Translation\Translator $translator)
    {
        $this->user = $user;
        $this->translator = $translator;
    }

    /**
     * @return Form
     */
    public function create()
    {
        $form = new Form();
        $form->addText('username', $this->translator->translate('messages.app.username') . ':')
            ->setRequired($this->translator->translate('messages.app.usernameRequired'));

        $form->addPassword('password', $this->translator->translate('messages.app.password') . ':')
            ->setRequired($this->translator->translate('messages.app.passwordRequired'));

        $form->addSubmit('send', $this->translator->translate('messages.app.login'));

        $form->onSuccess[] = array($this, 'formSucceeded');

        return $form;
    }

    public function formSucceeded($form, $values)
    {
        $this->user->setExpiration('15 hours', false);

        try {
            $this->user->login($values->username, $values->password);
        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }
}
