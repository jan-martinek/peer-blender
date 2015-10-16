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
    
    public function actionDefault($id)
    {
        $this->courseInfo->init($this->courseRepository->find($id));
    }
    
    public function renderDefault($id)
    {
        $course = $this->courseInfo->course;
        
        $this->template->course = $course;
        $this->template->units  = $course->units;
    }
    
    public function actionEnrolled($id)
    {
        $this->courseInfo->init($this->courseRepository->find($id));
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
    }
}
