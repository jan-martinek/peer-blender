<?php

namespace App\Presenters;

/**
 * Review presenter.
 */
class ReviewPresenter extends BasePresenter
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
    
    /** @var \Model\Repository\UserRepository @inject */
    public $userRepository;

    public function renderDefault($id)
    {
        $this->template->review = $review = $this->reviewRepository->find($id);
        $this->template->solution = $review->solution;
        $this->template->assignment = $review->solution->assignment;
    }
    
    public function renderWriteForUnit($id) {
        $this->template->unit = $unit = $this->unitRepository->find($id);
        $this->template->course = $unit->course;
        
        $reviewer = $this->userRepository->find($this->user->id);
        if (!$review = $this->reviewRepository->findUnfinishedReview($reviewer)) {
            $solution = $this->solutionRepository->findSolutionToReview($unit, $reviewer);
            $review = $this->reviewRepository->createReview($solution, $reviewer);
        } else {
            $solution = $review->solution;
        }
        $this->template->review = $review;
        $this->template->solution = $solution;
        $this->template->assignment = $solution->assignment;
    }
    
    public function renderObjection() {
        
    }
}
