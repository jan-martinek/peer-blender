<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Michelf\Markdown;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @persistent */
    public $locale;

    /** @var \Kdyby\Translation\Translator @inject */
    public $translator;
    
    /** @var \Model\Repository\FavoriteRepository @inject */
    public $favoriteRepository;
    
    /** @var \Model\Repository\UserRepository @inject */
    public $userRepository;
    
    protected $userEntity;

    public function startup()
    {
        parent::startup();
        
        $this->userEntity = $this->userRepository->find($this->user->id);
    }

    public function beforeRender() {
        //markdown
        $this->template->addFilter('md', function ($s) {
            return \Michelf\Markdown::defaultTransform($s);
        });
        //inline markdown
        $this->template->addFilter('imd', function ($s) {
            return strip_tags(Markdown::defaultTransform($s), '<a><strong><em>');
        });
    }

    protected function createTemplate($class = null)
    {
        $template = parent::createTemplate($class);

        $this->translator->createTemplateHelpers()
             ->register($template->getLatte());

        return $template;
    }

    public function handleLogout()
    {
        $user = $this->getUser();
        $user->logout();
        $this->redirect('Homepage:default');
    }
}
