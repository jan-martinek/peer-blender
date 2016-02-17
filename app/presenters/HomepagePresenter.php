<?php

namespace App\Presenters;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter
{
    /** @var \Model\Repository\CourseRepository @inject */
    public $courseRepository;

    public function renderDefault()
    {
         $this->template->courses = $this->courseFactory->produceMultiple(
         	$this->courseRepository->findAll());
    }
}
