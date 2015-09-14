<?php

namespace App\Presenters;

use App\Components\HomeworkForm;

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
    
    /** @var \Model\Repository\UserRepository @inject */
    public $userRepository;

    /** @var \Model\Repository\ReviewRepository @inject */
    public $reviewRepository;
    
    /** @var \Model\Repository\SolutionRepository @inject */
    public $solutionRepository;
    
    private $unit;
    private $assignment;
    private $questions;
    private $userEntity;

    public function actionDefault($id) {
        $this->userEntity = $this->userRepository->find($this->user->id);
        $this->unit = $this->unitRepository->find($id);
        $this->assignment = $this->assignmentRepository->getMyAssignment($this->unit, $this->userEntity);        
        $this->questions = unserialize($this->assignment->questions);
    }

    public function renderDefault($id)
    {
        $this->template->unit = $this->unit;
        $this->template->assignment = $this->assignment;
        $this->template->course = $this->courseRepository->find($this->unit->course->id);        
        $this->template->solution = $solution = $this->template->assignment->solution;
        if ($solution) {
            $this->template->answers = unserialize($solution->answer);
        }
        $this->template->reviews = $this->reviewRepository->findByUnitAndReviewer($this->unit, $this->userEntity);
    }
    
    protected function createComponentHomeworkForm() {
        if (!$this->questions) {
            throw new Nette\Application\BadRequestException;
        }
        
        return new HomeworkForm($this->assignment, $this->questions, $this->solutionRepository, $this->translator);
    }
}
