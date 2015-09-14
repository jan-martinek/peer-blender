<?php

namespace App\Presenters;

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

    public function renderDefault($id)
    {
        $user = $this->userRepository->find($this->user->id);
        $this->template->unit = $unit = $this->unitRepository->find($id);
        $this->template->course = $this->courseRepository->find($unit->course->id);        
        $this->template->assignment = $this->assignmentRepository->getMyAssignment($unit, $user);
        $this->template->solution = $solution = $this->template->assignment->solution;
        $this->template->reviews = $this->reviewRepository->findByUnitAndReviewer($unit, $user);
    }
}
