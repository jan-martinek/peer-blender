<?php

namespace Model;

class CourseInfo extends \Nette\Object
{
	
	/** @var \Model\Entity\Course */
	public $course;
	
	/** @var \Model\Entity\Unit */
	public $unit;
	
	/** @var \Model\Entity\Assignment */
	public $assignment;
	
	/** @var \Model\Entity\Solution */
	public $solution;
	
	/** @var \Model\Entity\Review */
	public $review;
	
	/** @var \Model\Entity\Objection */
	public $objection;
	
	/** @var \Model\Repository\FavoriteRepository */
    public $favoriteRepository;
    
    public function setFavoriteRepository($favoriteRepository)
    {
    	$this->favoriteRepository = $favoriteRepository;
    }
	
	public function insert($entity) 
	{
		if ($entity instanceof \Model\Entity\FavoritableEntity) {
			$entity->setFavoriteRepository($this->favoriteRepository);
		}
		
		$classname = get_class($entity);
		
		switch ($classname) {
			case "Model\Entity\Course":
				$this->setCourse($entity);
				break;
			case "Model\Entity\Unit":
				$this->setUnit($entity);
				break;
			case "Model\Entity\Assignment":
				$this->setAssignment($entity);
				break;
			case "Model\Entity\Solution":
				$this->setSolution($entity);
				break;
			case "Model\Entity\Review":
				$this->setReview($entity);
				break;
			case "Model\Entity\Objection":
				$this->setObjection($entity);
				break;
			default:
				throw new \Exception('"' . $classname . '" is not an object describing course.');
		}	
		
		return $entity;
	}
	
	public function setObjection($objection)
	{
		if (is_null($this->objection)) {
			$this->objection = $objection;	
		} else {
			throw new CourseInfoEntityAlreadyDefined;
		}
		
		if (is_null($this->review)) {
			$this->setReview($objection->review);	
		} else if ($this->review->id !== $objection->review->id) {
			throw new InconsistentCourseInfoChainException;
		}
	}
	
	public function setReview($review)
	{
		if (is_null($this->review)) {
			$this->review = $review;	
		} else {
			throw new CourseInfoEntityAlreadyDefined;
		}
		
		if (is_null($this->solution)) {
			$this->setSolution($review->solution);
		} else if ($this->solution->id !== $review->solution->id) {
			throw new InconsistentCourseInfoChainException;
		}
	}
	
	public function setSolution($solution)
	{
		if (is_null($this->solution)) {
			$this->solution = $solution;	
		} else {
			throw new CourseInfoEntityAlreadyDefined;
		}
		
		if (is_null($this->assignment)) {
			$this->setAssignment($solution->assignment);
		} else if ($this->assignment->id !== $solution->assignment->id) {
			throw new InconsistentCourseInfoChainException;
		}
	}
	
	public function setAssignment($assignment)
	{
		if (is_null($this->assignment)) {
			$this->assignment = $assignment;	
		} else {
			throw new CourseInfoEntityAlreadyDefined;
		}
		
		if (is_null($this->unit)) {
			$this->setUnit($assignment->unit);	
		} else if ($this->unit->id !== $assignment->unit->id) {
			throw new InconsistentCourseInfoChainException;
		}
	}
	
	public function setUnit($unit)
	{
		if (is_null($this->unit)) {
			$this->unit = $unit;	
		} else {
			throw new CourseInfoEntityAlreadyDefined;
		}
		
		if (is_null($this->course)) {
			$this->setCourse($unit->course);	
		} else if ($this->course->id !== $unit->course->id) {
			throw new InconsistentCourseInfoChainException;
		}
	}
	
	public function setCourse($course)
	{
		if (is_null($this->course)) {
			$this->course = $course;	
		} else {
			throw new CourseInfoEntityAlreadyDefined;
		}
	}
}

class CourseInfoEntityAlreadyDefined extends \Exception
{
}

class InconsistentCourseInfoChainException extends \Exception
{
}
