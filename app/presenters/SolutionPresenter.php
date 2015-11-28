<?php

namespace App\Presenters;

use DateTime;
use Model\Entity\Log;
use Model\Entity\Solution;
use Nette\Utils\Strings;

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
    
    /** @var \Model\Repository\SolutionRepository @inject */
    public $solutionRepository;
    
    /** @var \Model\UploadStorage @inject */
    public $uploadStorage;
    
    /** @var \Nette\Http\Request @inject */
    public $request;    
    
    public function actionDefault($id) 
    {     
        $solution = $this->solutionRepository->find($id);
        $this->setupCourseInfo($solution);
        
        if (!$this->courseInfo->unit->hasReviewsPhaseStarted() && !$this->user->isAllowed('solution', 'viewAnytime')) {
            throw new \Nette\Application\BadRequestException('Forbidden', 403);
        }
    }    
    
    public function renderDefault($id)
    {
        $solution = $this->courseInfo->solution;
        $this->template->solution = $solution;
        
        $this->template->answers = $solution->answers;
        
        $this->template->isFavorited = $solution->isFavoritedBy($this->userInfo);
        $this->template->unit = $this->courseInfo->unit; 
        $this->template->assignment = $this->courseInfo->assignment;
        $this->template->course = $this->courseInfo->course;   
        $this->template->uploadPath = $this->uploadStorage->path;
    }
    
    public function actionPreview()
    {
        $post = $this->request->getPost();
        $this->template->answer = $post['answer'];
    }
    
    public function handleFavorite() 
    {
        $this->courseInfo->solution->favorite($this->userRepository->find($this->user->id));
        $this->redirect('this');
    }
    
}
