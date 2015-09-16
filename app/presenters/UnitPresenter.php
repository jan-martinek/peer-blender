<?php

namespace App\Presenters;

use App\Components\HomeworkForm;
use DateTime;
use Model\Entity\Log;

/**
 * Unit presenter.
 */
class UnitPresenter extends BasePresenter
{
    /** @var \Model\Repository\CourseRepository @inject */
    public $courseRepository;
    
    /** @var \Model\Repository\UnitRepository @inject */
    public $unitRepository;
    
    /** @var \Model\Repository\AssignmentRepository @inject */
    public $assignmentRepository;

    /** @var \Model\Repository\ReviewRepository @inject */
    public $reviewRepository;
    
    /** @var \Model\Repository\SolutionRepository @inject */
    public $solutionRepository;
    
    private $course;
    private $unit;
    private $assignment;
    private $questions;
    private $solution;
    
    public function actionDefault($id) 
    {
        $this->unit = $this->unitRepository->find($id);
        $this->course = $this->unit->course;
        
        $this->unit->setFavoriteRepository($this->favoriteRepository);
        $this->template->isFavorited = $this->unit->isFavoritedBy($this->userEntity);
        
        $this->assignment = $this->assignmentRepository->getMyAssignment($this->unit, $this->userEntity);        
        $this->questions = unserialize($this->assignment->questions);
        
        $this->solution = $this->assignment->solution;
        
        $this->logEvent($this->unit, 'open');
    }

    public function renderDefault($id)
    {
        $this->template->unit = $this->unit; 
        $this->template->assignment = $this->assignment;
        $this->template->course = $this->course;
        $this->template->solution = $this->solution;
        
        if ($this->solution) {
            $this->template->answers = unserialize($this->solution->answer);
        }
        
        $this->template->reviews = $this->reviewRepository->findByUnitAndReviewer($this->unit, $this->userEntity);
    }
    
    public function handleFavorite() 
    {
        $this->unit->favorite($this->userRepository->find($this->user->id));
        $this->redirect('this');
    }
    
    protected function createComponentHomeworkForm() 
    {
        if (!$this->questions) {
            throw new Nette\Application\BadRequestException;
        }
        
        $form = new HomeworkForm($this->questions, $this->translator);
        $form->onSuccess[] = array($this, 'homeworkFormSucceeded');
        return $form;
    }
    
    public function homeworkFormSucceeded(HomeworkForm $form, $values) 
    {
        if ($solution = $this->assignment->solution) {
            $solution->edited_at = new DateTime;
            $solution->answer = serialize((array) $values->questions);
            $solution->attachment = 'TODO';
            $this->solutionRepository->persist($solution);
            $this->logEvent($solution, 'edit');
        } else {
            $solution = new Solution;
            $solution->unit = $this->unit;
            $solution->assignment = $this->assignment;
            $solution->user = $this->userEntity;
            $solution->submitted_at = new DateTime;
            $solution->edited_at = new DateTime;
            $solution->answer = serialize((array) $values->questions);
            $solution->attachment = 'TODO';
            $this->solutionRepository->persist($solution);            
            $this->logEvent($solution, 'create');
        }

        $this->redirect('this');
    }
}
