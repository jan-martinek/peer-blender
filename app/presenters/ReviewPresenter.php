<?php

namespace App\Presenters;

use App\Components\ReviewForm;
use Nette\Application\UI\Form;
use Nette\Utils\Html;
use Model\Entity\Objection;
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
    
    /** @var \Model\Repository\ObjectionRepository @inject */
    public $objectionRepository;
    
    /** @var \Model\UploadStorage @inject */
    public $uploadStorage;    

    public function actionDefault($id) 
    {
        $this->courseInfo->insert($this->reviewRepository->find($id));
        $this->template->isFavorited = $this->courseInfo->review->isFavoritedBy($this->userInfo);
        $this->template->uploadPath = $this->uploadStorage->path;
    }

    public function renderDefault($id)
    {   
        $review = $this->courseInfo->review;
        
        $this->template->review = $review;
        $this->template->solution = $review->solution;
        $this->template->assignment = $review->solution->assignment;
    }
    
    public function actionWriteForUnit($id) 
    {
        $unit = $this->unitRepository->find($id);
        $this->template->uploadPath = $this->uploadStorage->path;
        
        $this->template->unit = $unit;
        $this->template->course = $unit->course;
        $solution = null;
        
        try {
            // needs splitting into readable methods
            $reviewer = $this->userRepository->find($this->user->id);
            if (!$review = $this->reviewRepository->findUnfinishedReview($unit, $reviewer)) {
                if ($solution = $this->solutionRepository->findSolutionToReview($unit, $reviewer)) {
                    $review = $this->reviewRepository->createReview($solution, $reviewer);
                    $review = $this->reviewRepository->find($review->id); // fetching all columns
                    $this->logEvent($review, 'create');    
                } else {
                    return;
                }
            } else {
                $solution = $review->solution;
            }

            $this->template->review = $this->courseInfo->insert($review);
            $this->template->solution = $solution;
            $this->template->assignment = $assignment = $solution->assignment;
        } catch (\Model\Repository\SolutionToReviewNotFoundException $e) {
            $this->template->message = $this->translator->translate('messages.unit.noSolutionAvailable');   
        } catch (\Model\Repository\ReviewLimitReachedException $e) {
            $this->template->message = $this->translator->translate(
                'messages.unit.enoughReviews', 
                NULL, 
                array('count' => $unit->course->reviewCount)
            );
        }
    }
    
    public function renderWriteForUnit($id) 
    {

    }
    
    public function handleFavorite() 
    {
        $this->courseInfo->review->favorite($this->userRepository->find($this->user->id));
        $this->redirect('this');
    }    
    
    protected function createComponentReviewForm() 
    {
        if (!$this->courseInfo->assignment->rubrics) {
            throw new \Nette\Application\BadRequestException;
        }
        
        $form = new ReviewForm($this->courseInfo->review, $this->reviewRepository, $this->translator);
        $form->onSuccess[] = array($this, 'reviewFormSucceeded');
        return $form;
    }
    
    public function reviewFormSucceeded(ReviewForm $form, $values) 
    {
        $review->score = $values->score;
        $review->assessmentSet = $values->rubrics;
        $review->comments = $values->comments;
        $review->submitted_at = new DateTime;
        $this->reviewRepository->persist($review);
        $this->logEvent($review, 'submit');
        $this->redirect('this');
    }
    
    protected function createComponentObjectionForm() 
    {
        $form = new Form;
        $form->addTextarea('objection', $this->translator->translate('messages.objection.description'));
        $form->addCheckbox('ratingIsWrong', ' '.$this->translator->translate('messages.objection.ratingIsWrong'))
            ->setRequired($this->translator->translate('messages.objection.ratingIsWrongNeeded'));
        $form->addCheckbox('reasonsGiven', ' '.$this->translator->translate('messages.objection.reasonsGiven'))
            ->setRequired($this->translator->translate('messages.objection.reasonsGivenNeeded'));
        $form->addSubmit('submit', $this->translator->translate('messages.objection.raise'));
    
        $form->onSuccess[] = array($this, 'objectionFormSucceeded');
        return $form;
    }
    
    public function objectionFormSucceeded(Form $form, $values) 
    {   
        $objection = new Objection;
        $objection->objection = $values->objection;
        $objection->review = $this->courseInfo->review;
        $objection->user = $this->userRepository->find($this->user->id);
        $objection->submitted_at = new DateTime;        
        $this->objectionRepository->persist($objection);
        $this->logEvent($objection, 'submit');
        $this->redirect('this');
    }    
}
