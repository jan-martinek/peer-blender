<?php

namespace App\Presenters;

/**
 * Course presenter.
 */
class CoursePresenter extends BasePresenter
{
    /** @var \Model\Repository\CourseRepository @inject */
    public $courseRepository;

    /** @var \Model\Repository\EnrollmentRepository @inject */
    public $enrollmentRepository;
    
    /** @var \Model\Repository\UnitRepository @inject */
    public $unitRepository;
    
    /** @var \Model\Repository\SolutionRepository @inject */
    public $solutionRepository;
        
    /** @var \Model\Repository\ReviewRepository @inject */
    public $reviewRepository;
    
    public function actionDefault($id)
    {
        $this->courseInfo->insert($this->courseRepository->find($id));
    }
    
    public function renderDefault($id)
    {
        $course = $this->courseInfo->course;
        
        $this->template->course = $course;
        $this->template->units  = $course->units;
        
        $favoriteSolutions = array();
        foreach ($course->units as $unit) {
            $favoriteSolutions[$unit->id] = $this->solutionRepository->findFavoriteByUnit($unit);
        }
        
        $this->template->favoriteSolutions = $favoriteSolutions;
        
        $favoriteReviews = array();
        foreach ($course->units as $unit) {
            $favoriteReviews[$unit->id] = $this->reviewRepository->findFavoriteByUnit($unit);
        }
        
        $this->template->favoriteReviews = $favoriteReviews;
    }
    
    public function actionEnrolled($id)
    {
        $this->courseInfo->insert($this->courseRepository->find($id));
    }
    
    public function renderEnrolled($id) 
    {   
        $course = $this->courseInfo->course;
        
        $this->template->course = $course;
        $ids = $this->enrollmentRepository->findAllUserIds($course);
        $this->template->userFavorites = $this->favoriteRepository->findAllByScope('User', $ids);
    }
    
    public function renderStats($id) 
    {
        $this->template->course = $course = $this->courseRepository->find($id);
        
        $role = $this->enrollmentRepository->getRoleInCourse($this->userInfo, $course);
        if (!in_array($role, array('admin', 'assistant'))) {
            throw new \Nette\Application\BadRequestException('Forbidden', 403);
        }
        
        $this->template->reviewStats = $this->courseRepository->getReviewStats($this->template->course);  
        $this->template->submittedReviews = $this->courseRepository->getSubmittedReviewsStats($this->template->course);
    }
}
