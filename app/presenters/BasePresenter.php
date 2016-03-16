<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Michelf\Markdown;
use Model\Entity\Log;
use App\Components\PhasesControl;
use App\Components\IQuestionsControlFactory;
use App\Components\ReviewsControl;
use App\Components\CourseGaControl;
use DateTime;
use ReflectionClass;


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
    
    /** @var \App\Components\IQuestionsControlFactory @inject */
    public $questionsControlFactory;
    
    /** @var \App\Components\IChatControlFactory @inject */
    public $chatControlFactory;
    
    /** @var \Model\CourseRegistry @inject */
    public $courseRegistry;
    
    /** @var \Model\Entity\User */
    public $userInfo;
    
    /** @var \Model\Ontology\CourseFactory @inject */
    public $courseFactory;    
    

    public function startup()
    {
        parent::startup();
        
        $this->courseRegistry->setFavoriteRepository($this->favoriteRepository);
        
        if ($this->user->isLoggedIn() OR in_array($this->getName(), array('Homepage', 'Sign', 'Password'))) {
            $this->userInfo = $this->user->id ? $this->userRepository->find($this->user->id) : NULL;
        } else {
            $this->flashMessage('Please sign in.');
            
            $backlink = $this->storeRequest('+ 18 hour');
            $this->redirect('Sign:in', $backlink);
        }
    }
    
    /**
     * Produces an entity or array of entities and plugs
     * them into the template.
     * @param Model\Entity\Entity|array
     * @param string|null name of the entity
     */
    public function deliver($entity, $varName = null) 
    {
        if (is_null($varName)) {
            if (is_array($entity)) {
                $values = array_values($entity);
                $reflect = new ReflectionClass($values[0]);
                $varName = strtolower($reflect->getShortName()) . 's';    
            } else {
                $reflect = new ReflectionClass($entity);
                $varName = strtolower($reflect->getShortName());
            }
        }
        
        $this->template->$varName = $this->produce($entity);
    }
    
    /**
     * Produces an entity or array of entities via
     * the CourseFactory
     * @param Model\Entity\Entity|array
     */
    public function produce($entities) {
        if (is_array($entities)) {
            return $this->courseFactory->produceMultiple($entities);
        } else {
            return $this->courseFactory->produce($entities);
        }
    }
    
    public function register($entity) {
        return $this->courseRegistry->insert($entity);
    }
    
    public function setupCourseRegistry($entity) 
    {
        $this->courseRegistry->insert($entity);
        
        if ($this->user->isLoggedIn()) {
            $this->setupCourseRole();
        }
        
        return $entity;
    }
    
    public function setupCourseRole() 
    {
        $roles = $this->user->identity->roles;
        foreach ($roles as $key => $role) {
            if (strpos($role, 'course-') === 0) {
                unset($roles[$key]);
            }
        }

        if (is_null($this->courseRegistry)) {
            throw new Exception('Course is not defined.');
            return;            
        }
        if (is_null($this->userInfo)) {
            throw new Exception('User is not defined');
            return;
        }
        
        $courseRole = $this->enrollmentRepository->getRoleInCourse($this->userInfo, $this->courseRegistry->course);
        if (!is_null($courseRole) && $courseRole) {
            $roles[] = 'course-' . $courseRole;
        }
        
        $this->user->identity->roles = $roles;
    }

    public function beforeRender() 
    {
        //markdown
        $this->template->addFilter('md', function ($s) {
            return \Michelf\Markdown::defaultTransform($s);
        });
        //inline markdown
        $this->template->addFilter('imd', function ($s) {
            return strip_tags(Markdown::defaultTransform($s), '<a><strong><em>');
        });
        //stars
        $this->template->addFilter('stars', function ($s) {
            return \App\Components\ReviewForm::renderRatingStars($s);
        });
    }

    protected function createTemplate($class = null)
    {
        $template = parent::createTemplate($class);

        $this->translator->createTemplateHelpers()
             ->register($template->getLatte());

        return $template;
    }
    
    public function logEvent(\LeanMapper\Entity $entity, $action) 
    {
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
        $this->logEvent($this->userInfo, 'logout');
        $user->logout();
        $this->redirect('Homepage:default');
    }
    
    protected function createComponentChatRenderer()
    {
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
    
    protected function createComponentCourseGaRenderer()
    {
        return new CourseGaControl();
    }
}
