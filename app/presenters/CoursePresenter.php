<?php

namespace App\Presenters;

/**
 * Course presenter.
 */
class CoursePresenter extends BasePresenter
{
    /** @var \Model\Repository\CourseRepository @inject */
    public $courseRepository;
    
    /** @var \Model\Repository\UnitRepository @inject */
    public $unitRepository;
    
    /** @var \Model\Repository\SolutionRepository @inject */
    public $solutionRepository;
        
    /** @var \Model\Repository\ReviewRepository @inject */
    public $reviewRepository;
    
    public function actionDefault($id)
    {
        $this->setupCourseInfo($this->courseRepository->find($id));
    }
    
    public function renderDefault($id)
    {
        $factory = $this->courseFactory;
        $info = $this->courseInfo;
        
        $course = $this->courseInfo->course;
        
        $this->template->course = $factory->produce($course);
        $this->template->units = $factory->produceMultiple($course->units);
        
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
    
        $this->template->reviewsWithProblems = $this->reviewRepository->findReviewsWithProblemsByUserAndCourse(
            $this->userInfo, 
            $course
        );
    }
    
    public function actionEnrolled($id)
    {
        $this->setupCourseInfo($this->courseRepository->find($id));
    }
    
    public function renderEnrolled($id) 
    {   
        $course = $this->courseInfo->course;
        
        $this->template->course = $course;
        $ids = $this->enrollmentRepository->findAllUserIds($course);
        $this->template->userFavorites = $this->favoriteRepository->findAllByScope('User', $ids);
    }
    
    public function renderObjections($id)
    {
        $this->courseInfo->insert($this->courseRepository->find($id));
        $this->template->course = $course = $this->courseInfo->course;
        $this->template->reviews = $this->reviewRepository->findReviewsWithProblemsByCourse($course);
    }
    
    public function actionStats($id) 
    {
        $this->setupCourseInfo($this->courseRepository->find($id));
    }
    
    public function renderStats($id) 
    {
        $course = $this->courseInfo->course;
        
        if (!$this->user->isAllowed('course', 'viewStats')) {
            throw new \Nette\Application\BadRequestException('Forbidden', 403);
        }
        
        $this->template->course = $course;
        $this->template->reviewStats = $this->courseRepository->getReviewStats($course);  
        $this->template->submittedReviews = $this->courseRepository->getSubmittedReviewsStats($course);
        $this->template->problems = $this->courseRepository->getProblemReviewsStats($course);  
    }
}
