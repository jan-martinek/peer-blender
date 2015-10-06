<?php

namespace App\Presenters;

use App\Components\ReviewForm;
use DateTime;

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
    
    private $review;


    public function actionDefault($id) 
    {
        $this->review = $this->reviewRepository->find($id);
        $this->review->setFavoriteRepository($this->favoriteRepository);
        $this->template->isFavorited = $this->review->isFavoritedBy($this->userEntity);
    }

    public function renderDefault($id)
    {   
        $this->template->review = $this->review;
        $this->template->solution = $this->review->solution;
        $this->template->assignment = $this->review->solution->assignment;
        $this->template->gaCode = $this->solution->unit->course->gaCode;
    }
    
    public function actionWriteForUnit($id) 
    {
        $unit = $this->unitRepository->find($id);
        
        $this->template->unit = $unit;
        $this->template->course = $unit->course;
        
        $reviewer = $this->userRepository->find($this->user->id);
        if (!$review = $this->reviewRepository->findUnfinishedReview($reviewer)) {            
            if ($solution = $this->solutionRepository->findSolutionToReview($unit, $reviewer)) {
                $review = $this->reviewRepository->createReview($solution, $reviewer);
                $this->logEvent($review, 'create');    
            } else {
                $solution = null;
                return;
            }
            
        } else {
            $solution = $review->solution;
        }
        $this->review = $this->template->review = $review;
        $this->template->solution = $solution;
        $this->template->assignment = $assignment = $solution->assignment;
    }
    
    public function renderWriteForUnit($id) 
    {

    }
    
    public function renderObjection($id) 
    {
        
    }
    
    public function handleFavorite() 
    {
        $this->review->favorite($this->userRepository->find($this->user->id));
        $this->redirect('this');
    }    
    
    protected function createComponentReviewForm() 
    {
        if (!$this->review->solution->assignment->rubrics) {
            throw new Nette\Application\BadRequestException;
        }
        
        $form = new ReviewForm($this->review, $this->reviewRepository, $this->translator);
        $form->onSuccess[] = array($this, 'reviewFormSucceeded');
        return $form;
    }
    
    public function reviewFormSucceeded(ReviewForm $form, $values) 
    {
        $this->review->score = $values->score;
        $this->review->assessmentSet = $values->rubrics;
        $this->review->comments = $values->comments;
        $this->review->submitted_at = new DateTime;
        $this->reviewRepository->persist($this->review);
        $this->logEvent($this->review, 'submit');
        $this->redirect('this');
    }
}
