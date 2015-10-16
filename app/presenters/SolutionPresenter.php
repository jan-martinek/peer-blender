<?php

namespace App\Presenters;

use App\Components\HomeworkForm;
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
    
    public function actionDefault($id) 
    {     
        $solution = $this->solutionRepository->find($id);
        $solution->setFavoriteRepository($this->favoriteRepository);
        $this->courseInfo->init($solution);
    }    
    
    public function renderDefault($id)
    {
        $solution = $this->courseInfo->solution;
        $this->template->solution = $solution;
        $this->template->answers = $solution->answerSet;
        
        $this->template->isFavorited = $solution->isFavoritedBy($this->userInfo);
        $this->template->unit = $this->courseInfo->unit; 
        $this->template->assignment = $this->courseInfo->assignment;
        $this->template->course = $this->courseInfo->course;   
        $this->template->uploadPath = $this->uploadStorage->path;
    }
    
    public function handleFavorite() 
    {
        $this->courseInfo->solution->favorite($this->userRepository->find($this->user->id));
        $this->redirect('this');
    }
    
}
