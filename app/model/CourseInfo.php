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
	
	public function init($entity) 
	{
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
		$this->objection = $objection;
		if (is_null($this->review)) {
			$this->setReview($objection->review);	
		}
	}
	
	public function setReview($review) 
	{
		$this->review = $review;
		if (is_null($this->solution)) {
			$this->setSolution($review->solution);
		}
	}
	
	public function setSolution($solution) 
	{
		$this->solution = $solution;
		if (is_null($this->assignment)) {
			$this->setAssignment($solution->assignment);
		}
	}
	
	public function setAssignment($assignment)
	{
		$this->assignment = $assignment;
		if (is_null($this->unit)) {
			$this->setUnit($assignment->unit);	
		}
	}
	
	public function setUnit($unit)
	{
		$this->unit = $unit;
		if (is_null($this->course)) {
			$this->setCourse($unit->course);	
		}
	}
	
	public function setCourse($course)
	{
		$this->course = $course;
	}
}
