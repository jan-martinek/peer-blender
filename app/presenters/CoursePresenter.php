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
        $course = $this->courseInfo->course;
        
        $this->deliver($course);
        $this->deliver($course->units);
        
        $this->template->favoriteSolutions = $this->solutionRepository->findFavoriteByUnits($course->units);
        $this->template->favoriteReviews = $this->reviewRepository->findFavoriteByUnits($course->units);
     
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
