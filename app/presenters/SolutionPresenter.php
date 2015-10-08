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
    
    public function actionDefault($id) {
        
    }    
    
    public function renderDefault($id)
    {
        $solution = $this->solutionRepository->find($id);
        $assignment = $solution->assignment;
        $unit = $assignment->unit;
        $course = $unit->course;
        
        $questions = $assignment->questionSet;
        $solution = $assignment->solution;
        $solution->setFavoriteRepository($this->favoriteRepository);
        
        $this->template->isFavorited = $solution->isFavoritedBy($this->userEntity);
        $this->template->unit = $unit; 
        $this->template->assignment = $assignment;
        $this->template->course = $course;
        $this->template->solution = $solution;
        $this->template->answers = $solution->answerSet;
        $this->template->gaCode = $course->gaCode;
        $this->template->uploadPath = $this->uploadStorage->path;
    }
    
    public function handleFavorite() 
    {
        $this->solution->favorite($this->userRepository->find($this->user->id));
        $this->redirect('this');
    }
    
}
