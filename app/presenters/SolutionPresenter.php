<?php

namespace App\Presenters;

use DateTime;
use Model\Entity\Log;
use Model\Entity\Solution;
use Model\Entity\Review;
use Model\Entity\ReviewComment;
use Nette\Utils\Strings;
use Nette\Application\UI\Form;

/**
 * Unit presenter.
 */
class SolutionPresenter extends BasePresenter
{
    /** @var \Model\Repository\CourseRepository @inject */
    public $courseRepository;
    
    /** @var \Model\Repository\UnitRepository @inject */
    public $unitRepository;
    
    /** @var \Model\Repository\AssignmentRepository @inject */
    public $assignmentRepository;

    /** @var \Model\Repository\ReviewRepository @inject */
    public $reviewRepository;
    
    /** @var \Model\Repository\ReviewCommentRepository @inject */
    public $reviewCommentRepository;
    
    /** @var \Model\Repository\SolutionRepository @inject */
    public $solutionRepository;
    
    /** @var \Model\UploadStorage @inject */
    public $uploadStorage;

    /** @var \Model\Repository\LogRepository @inject */
    public $logRepository;
    
    /** @var \Nette\Http\Request @inject */
    public $request;    
    
    public function actionDefault($id) 
    {     
        $solution = $this->solutionRepository->find($id);
        $this->setupCourseInfo($solution);
        
        if (!$this->courseInfo->unit->hasReviewsPhaseStarted() && !$this->user->isAllowed('solution', 'viewAnytime')) {
            throw new \Nette\Application\BadRequestException('Forbidden', 403);
        }
        
        if ($this->user->isAllowed('solution', 'viewLog')) {
            $this->template->solutionLog = $this->logRepository->findByEntity('Solution', $id);    
        }        
    }    
    
    public function renderDefault($id)
    {   
        $this->deliver($this->courseInfo->unit); 
        $this->deliver($this->courseInfo->assignment);
        $this->deliver($this->courseInfo->course);   
        
        $solution = $this->courseInfo->solution;
        $this->template->solution = $solution;
        $this->template->answers = $solution->answers;
        $this->template->isFavorited = $solution->isFavoritedBy($this->userInfo);
        $this->template->uploadPath = $this->uploadStorage->path;
    }
    
    public function actionPreview()
    {
        $post = $this->request->getPost();
        $this->template->answer = isset($post['answer']) ? $post['answer'] : '';
    }
    
    public function handleFavorite() 
    {
        $this->courseInfo->solution->favorite($this->userRepository->find($this->user->id));
        $this->redirect('this');
    }
    
    
    protected function createComponentAddReviewForm() 
    {
        $enrollments = $this->enrollmentRepository->findAllByCourse($this->courseInfo->course);
        $options = array();
        foreach ($enrollments as $enrollment) {
            $user = $enrollment->user;
            $options[$user->id] = $user->name;    
        }

        $form = new Form;
        $form->addSelect(
            'user_id', 
            $this->translator->translate('messages.course.roles.student'),
            $options
        )->setPrompt($this->translator->translate('messages.course.roles.student'));
        
        $form->addSubmit('submit', $this->translator->translate('messages.solution.createAdHoc'));
        $form->onSuccess[] = array($this, 'addReviewFormSucceeded');
        return $form;
    }
    
    public function addReviewFormSucceeded(Form $form, $values) 
    {
        $reviewer = $this->userRepository->find($values->user_id);
        $solution = $this->courseInfo->solution;
        
        $review = $this->reviewRepository->createReview($solution, $reviewer);
        $review->status = Review::PROBLEM;
        $this->reviewRepository->persist($review);  
        
        $comment = new ReviewComment;
        $comment->comment = '<span style="color:#aaa">— ' . $this->translator->translate('messages.review.addedAdHoc') . ' —</span>';
        $comment->review = $review;
        $comment->review_status = $review->status;
        $comment->author = $this->userRepository->find($this->user->id);
        $comment->submitted_at = new DateTime;        
        $this->reviewCommentRepository->persist($comment);
        
        $this->redirect('this');
    }
    
}
