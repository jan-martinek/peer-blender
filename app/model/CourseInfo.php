<?php

namespace Model;

use Model\Entity\Course;
use Model\Entity\Unit;
use Model\Entity\Assignment;
use Model\Entity\Solution;
use Model\Entity\Review;
use Model\Entity\ReviewComment;

class CourseInfo extends \Nette\Object
{
	
	/** @var Course */
	public $course;
	
	/** @var Unit */
	public $unit;
	
	/** @var Assignment */
	public $assignment;
	
	/** @var Solution */
	public $solution;
	
	/** @var Review */
	public $review;
	
	/** @var ReviewComment */
	public $reviewComment;
	
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
			case "Model\Entity\ReviewComment":
				$this->setReviewComment($entity);
				break;
			default:
				throw new \Exception('"' . $classname . '" is not an object describing course.');
		}	
		
		return $entity;
	}
	
	public function setReviewComment(ReviewComment $reviewComment)
	{
		if (is_null($this->reviewComment)) {
			$this->reviewComment = $reviewComment;	
		} else {
			throw new CourseInfoEntityAlreadyDefined;
		}
		
		if (is_null($this->review)) {
			$this->setReview($reviewComment->review);	
		} else if ($this->review->id !== $reviewComment->review->id) {
			throw new InconsistentCourseInfoChainException;
		}
	}
	
	public function setReview(Review $review)
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
	
	public function setSolution(Solution $solution)
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
	
	public function setAssignment(Assignment $assignment)
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
	
	public function setUnit(Unit $unit)
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
	
	public function setCourse(Course $course)
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
