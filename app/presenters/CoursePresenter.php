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

    public function renderDefault($id)
    {
        $this->template->course = $this->courseRepository->find($id);
        $this->template->units = $this->unitRepository->findByCourseId($id);
        $this->template->gaCode = $this->template->course->gaCode;
    }
    
    public function renderEnrolled($id) 
    {
        $this->template->course = $this->courseRepository->find($id);   
        $this->template->gaCode = $this->template->course->gaCode;
    }
}
