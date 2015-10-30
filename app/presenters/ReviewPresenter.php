<?php

namespace App\Presenters;

use App\Components\ReviewForm;
use App\Components\ReviewCommentForm;
use Nette\Application\UI\Form;
use Nette\Utils\Html;
use Model\Entity\Objection;
use Model\Entity\Review;
use Model\Entity\ReviewComment;
use DateTime;
use Exception;

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
    
    /** @var \Model\Repository\ReviewCommentRepository @inject */
    public $reviewCommentRepository;
    
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
        $unit = $this->courseInfo->insert($this->unitRepository->find($id));
        $role = $this->enrollmentRepository->getRoleInCourse($this->userInfo, $this->courseInfo->course);
        if (!$unit->isCurrentPhase($unit::REVIEWS) && !in_array($role, array('admin', 'assistant'))) {
            throw new \Nette\Application\BadRequestException('Forbidden', 403);
        }
        
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
    
    public function actionFix($id)
    {        
        $review = $this->reviewRepository->find($id);
        if (!$review->hasProblem()) {
            $this->flashMessage($this->translator->translate('messages.review.fixingIsNotPossibleNow'), 'alert');
            $this->redirect('Review:default', $id);
        }
        if ($review->reviewed_by->id != $this->userInfo->id) {
            $this->flashMessage($this->translator->translate('messages.review.fixOnlyYourOwn'), 'alert');
            $this->redirect('Review:default', $id);   
        }
        $this->courseInfo->insert($review);
    }
    
    public function renderFix($id) 
    {
        $this->template->review = $this->courseInfo->review;
        $this->template->assignment = $this->courseInfo->assignment;
        $this->template->solution = $this->courseInfo->solution;
        $this->template->unit = $this->courseInfo->unit;
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
        $review = $this->courseInfo->review;
        $review->score = $values->score;
        $review->assessmentSet = $values->rubrics;
        $review->notes = $values->notes;
        $review->submitted_at = new DateTime;
        if ($values->complete) {
            switch ($this->getAction()) {
                case 'writeForUnit':
                    $review->status = Review::OK;
                    break;
                case 'fix':
                    $review->status = Review::FIXED;
                    break;      
            }
        }
        $this->reviewRepository->persist($review);
        $this->logEvent($review, 'submit');
        
        switch ($this->getAction()) {
            case 'fix':
                $this->redirect('Review:default', $review->id);
                break;      
            default:
                $this->redirect('this');
        }
    }
    
    protected function createComponentReviewCommentForm()
    {
        $review = $this->courseInfo->review;
        $user = $this->userInfo;
        $courseRole = $this->enrollmentRepository->getRoleInCourse($this->userInfo, $this->courseInfo->course);        
        
        if ($review->isOk() && $review->reviewed_by->id == $user->id) {
            $statuses = $this->getReviewCommentFormStatuses('objectionRaisingOrCommenting');
        } elseif ($review->hasProblem() && $review->reviewed_by->id == $user->id) {
            $statuses = $this->getReviewCommentFormStatuses('reviewFixing');
        } elseif ($review->isObjected() && in_array($courseRole, array('admin', 'assistant'))) {
            $statuses = $this->getReviewCommentFormStatuses('objectionEvaluation');
        } elseif ($review->isFixed() && in_array($courseRole, array('admin', 'assistant'))) {
            $statuses = $this->getReviewCommentFormStatuses('fixEvaluation');
        } else {
            $statuses = $this->getReviewCommentFormStatuses($review->status);
        }
        
        $form = new Form;
        $form->addTextarea('comment', $this->translator->translate('messages.review.comments.label'));
        $form->addSelect('reviewStatus', $this->translator->translate('messages.review.status.title'), $statuses);
        $form->addSubmit('submit', $this->translator->translate('messages.review.comments.post'));
    
        $form->onSuccess[] = array($this, 'reviewCommentFormSucceeded');
        return $form;
    }
    
    private function getReviewCommentFormStatuses($kind) 
    {
        $statuses = array(
            'prep' => $this->translator->translate('messages.review.status.prep'),
            'ok' => $this->translator->translate('messages.review.status.ok'),
            'problem' => $this->translator->translate('messages.review.status.problem'),
            'objection' => $this->translator->translate('messages.review.status.objection'),
            'fixed' => $this->translator->translate('messages.review.status.fixed')
        );
        
        $availableStatuses = array();
        
        switch($kind) {
            case 'objectionRaisingOrCommenting':
                $availableStatuses['ok'] = $statuses['ok'];
                $availableStatuses['objection'] = $statuses['objection'];
                break;
            case 'objectionEvaluation':
                $availableStatuses['objection'] = $statuses['objection'];
                $availableStatuses['problem'] = $statuses['problem'];
                $availableStatuses['ok'] = $statuses['ok'];
                break;
            case 'reviewFixing':
                $availableStatuses['problem'] = $statuses['problem'];
                $availableStatuses['fixed'] = $statuses['fixed'];
                break;
            case 'fixEvaluation':
                $availableStatuses['fixed'] = $statuses['fixed'];
                $availableStatuses['problem'] = $statuses['problem'];
                $availableStatuses['ok'] = $statuses['ok'];
                break;
            default:
                if (in_array($kind, $statuses)) {
                    $availableStatuses[$kind] = $statuses[$kind];    
                } else {
                    throw new \Exception;
                }
        }
        
        return $availableStatuses;
    }
    
    public function reviewCommentFormSucceeded(Form $form, $values) 
    {   
        $comment = new ReviewComment;
        $comment->comment = $values->comment;
        $comment->review = $this->courseInfo->review;
        $comment->review_status = $values->reviewStatus;
        $comment->author = $this->userRepository->find($this->user->id);
        $comment->submitted_at = new DateTime;        
        $this->reviewCommentRepository->persist($comment);
        
        $review = $this->courseInfo->review;
        $review->status = $values->reviewStatus;
        $this->reviewRepository->persist($review);
        
        $this->logEvent($comment, 'submit');
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
