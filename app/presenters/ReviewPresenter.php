<?php

namespace App\Presenters;

use App\Components\ReviewForm;
use App\Components\ReviewCommentForm;
use Nette\Application\UI\Form;
use Nette\Utils\Html;
use Model\Entity\Review;
use Model\Entity\Unit;
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
    
    /** @var \Model\Repository\ReviewCommentRepository @inject */
    public $reviewCommentRepository;
    
    /** @var \Model\UploadStorage @inject */
    public $uploadStorage;    

    public function actionDefault($id) 
    {
        $this->setupCourseInfo($this->reviewRepository->find($id));
        $this->template->isFavorited = $this->courseInfo->review->isFavoritedBy($this->userInfo);
        $this->template->uploadPath = $this->uploadStorage->path;
    }

    public function renderDefault($id)
    {   
        $review = $this->courseInfo->review;
        
        $this->template->review = $review;
        $this->template->solution = $review->solution;
        
        $this->deliver($review->solution->assignment);
        $this->deliver($review->solution->assignment->unit);
    }
    
    public function actionWriteForUnit($id) 
    {
        $unit = $this->setupCourseInfo($this->unitRepository->find($id));
        $this->deliver($unit);
        
        if (!$unit->isCurrentPhase(Unit::REVIEWS) AND !$this->user->isAllowed('review', 'writeAnytime')) {
            throw new \Nette\Application\BadRequestException('Forbidden', 403);
        }
        
        $this->template->uploadPath = $this->uploadStorage->path;
        
        $this->deliver($unit->course);
        
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

            $this->template->review = $this->setupCourseInfo($review);
            $this->template->solution = $solution;
            $assignment = $solution->assignment;
        $this->template->assignment = $this->produce($assignment);
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
    
    public function handleUnlock($id)
    {
        $review = $this->courseInfo->review;
        $review->status = 'prep';
        $this->reviewRepository->persist($review);
        
        $comment = new ReviewComment;
        $comment->comment = '<span style="color:#aaa">— ' . $this->translator->translate('messages.review.unlocked') . ' —</span>';
        $comment->review = $review;
        $comment->review_status = $review->status;
        $comment->author = $this->userRepository->find($this->user->id);
        $comment->submitted_at = new DateTime;        
        $this->reviewCommentRepository->persist($comment);
        
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
                    
                    $comment = new ReviewComment;
                    $comment->comment = '<span style="color:#aaa">' . $this->translator->translate('messages.review.fixed') . '</span>';
                    $comment->review = $review;
                    $comment->review_status = $review->status;
                    $comment->author = $this->userRepository->find($this->user->id);
                    $comment->submitted_at = new DateTime;        
                    $this->reviewCommentRepository->persist($comment);
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
        $viewerMadeSolution = $review->solution->user->id === $this->userInfo->id;
        $viewerWroteReview = $review->reviewed_by->id === $this->userInfo->id;
        
        if ($this->user->isAllowed('review', 'evaluateObjection') && !$viewerMadeSolution && $review->isObjected()) {
            $statuses = $this->getReviewCommentFormStatuses('objectionEvaluation');
        } elseif ($this->user->isAllowed('review', 'evaluateFix') && !$viewerMadeSolution && $review->isFixed()) {
            $statuses = $this->getReviewCommentFormStatuses('fixEvaluation');
        } elseif ($this->user->isAllowed('review', 'announceProblem') && !$viewerMadeSolution && $review->isOk()) {
            $statuses = $this->getReviewCommentFormStatuses('problemAnnouncing');
        } elseif ($review->isOk() && $viewerMadeSolution) {
            $statuses = $this->getReviewCommentFormStatuses('objectionRaisingOrCommenting');
        } elseif ($review->hasProblem() && $viewerWroteReview) {
            $statuses = $this->getReviewCommentFormStatuses('reviewFixing');
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
            case 'problemAnnouncing':
                $availableStatuses['ok'] = $statuses['ok'];
                $availableStatuses['problem'] = $statuses['problem'];
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
                if (array_key_exists($kind, $statuses)) {
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
}
