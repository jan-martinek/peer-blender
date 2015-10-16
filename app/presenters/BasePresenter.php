<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Michelf\Markdown;
use Model\Entity\Log;
use App\Components\PhasesControl;
use App\Components\IQuestionsControlFactory;
use App\Components\ReviewsControl;
use DateTime;


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
    
    /** @var \Model\Repository\EnrollmentRepository @inject */
    public $enrollmentRepository;
    
    /** @var \Model\Repository\LogRepository @inject */
    public $logRepository;
    
    /** @var \Nette\Http\Response @inject */
    public $response;        
    
    protected $questionsControlFactory;
    /** @var \App\Components\IQuestionsControlFactory @inject */
    public $questionsControlFactory;
    
    protected $userEntity;
    /** @var \App\Components\IChatControlFactory @inject */
    public $chatControlFactory;
    

    public function startup()
    {
        parent::startup();
        
        if ($this->user->isLoggedIn() OR in_array($this->getName(), array('Homepage', 'Sign', 'Password'))) {
            $this->userEntity = $this->user->id ? $this->userRepository->find($this->user->id) : NULL;
        } else {
            $this->flashMessage('Please sign in.');
            
            $backlink = $this->storeRequest('+ 18 hour');
            $this->redirect('Sign:in', $backlink);
        }
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
    
    protected function logEvent(\LeanMapper\Entity $entity, $action) {
        $log = new Log;
        $log->entity_name = $entity->getConventionalName();
        $log->entity_identifier = $entity->id;
        $log->user = $this->userRepository->find($this->user->id);
        $log->logged_at = new DateTime;
        $log->action = $action;
        $this->logRepository->persist($log);
    }

    public function handleLogout()
    {
        $user = $this->getUser();
        $this->logEvent($this->userEntity, 'logout');
        $user->logout();
        $this->redirect('Homepage:default');
    }
    
    public function injectQuestionsControlFactory(IQuestionsControlFactory $factory)
    protected function createComponentChatRenderer()
    {
        $this->questionsControlFactory = $factory;
        $chatControl = $this->chatControlFactory->create();
        return $chatControl;
    }
    
    protected function createComponentQuestionsRenderer()
    {
        return $this->questionsControlFactory->create();
    }

    protected function createComponentPhasesRenderer()
    {
        return new PhasesControl();
    }
    
    protected function createComponentReviewsRenderer()
    {
        return new ReviewsControl();
    }
}
