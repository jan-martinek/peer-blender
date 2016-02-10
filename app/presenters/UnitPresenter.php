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

    /** @var \Model\Repository\AnswerRepository @inject */
    public $answerRepository;
    
    /** @var \Model\Repository\QuestionRepository @inject */
    public $questionRepository;
    
    /** @var \Model\UploadStorage @inject */
    public $uploadStorage;
    
    public function actionDefault($id, $lateEdits = FALSE) 
    {   
        $unit = $this->setupCourseInfo($this->unitRepository->find($id));
        if (!$unit->hasBeenPublished()) {
            throw new \Nette\Application\BadRequestException(NULL, 404);
            return;
        }
        
        $assignment = $this->assignmentRepository->getMyAssignment($this->courseInfo->unit, $this->userInfo, $this->questionRepository, $this->courseDefinition);
        $this->setupCourseInfo($assignment);
        if (isset($assignment->solution)) {
            $this->courseInfo->setSolution($assignment->solution);
        }
        
        $this->template->lateEdits = $lateEdits;
        
        $this->template->isFavorited = $unit->isFavoritedBy($this->userInfo);
        $this->logEvent($unit, 'open');
    }

    public function renderDefault($id)
    {
        $this->template->unit = $this->courseInfo->unit;
        $this->template->assignment = $this->courseInfo->assignment;
        $this->template->course = $this->courseInfo->course;
        $this->template->solution = $this->courseInfo->solution;
        
        if ($this->courseInfo->solution) {
            $this->template->answers = $this->courseInfo->solution->answers;
        }
        
        $this->template->uploadPath = $this->uploadStorage->path;
        $this->template->reviews = $this->reviewRepository->findByUnitAndReviewer($this->courseInfo->unit, $this->userInfo);
    }
    
    public function handleFavorite() 
    {
        $this->courseInfo->unit->favorite($this->userRepository->find($this->user->id));
        $this->redirect('this');
    }
    
    protected function createComponentHomeworkForm() 
    {
        if (!$this->courseInfo->assignment) {
            throw new \Nette\Application\BadRequestException;
        }
        
        $form = new HomeworkForm(
            $this,
            $this->courseInfo->assignment->questions
        );
        
        return $form;
    }
    
    
}
