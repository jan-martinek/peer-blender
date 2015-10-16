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

    public function renderDefault($id)
    {
        $this->template->course = $this->courseRepository->find($id);
        $this->template->units = $this->unitRepository->findByCourseId($id);
    }
    
    public function renderEnrolled($id) 
    {
        $this->template->course = $course = $this->courseRepository->find($id);
        $ids = $this->enrollmentRepository->findAllUserIds($course);
        $this->template->userFavorites = $this->favoriteRepository->findAllByScope('User', $ids);
    }
    
    public function renderStats($id) 
    {
        $this->template->course = $course = $this->courseRepository->find($id);
        
        $role = $this->enrollmentRepository->getRoleInCourse($this->userEntity, $course);
        if (!in_array($role, array('admin', 'assistant'))) {
            throw new \Nette\Application\BadRequestException('Forbidden', 403);
        }
        
        $this->template->reviewStats = $this->courseRepository->getReviewStats($this->template->course);
    }
}
