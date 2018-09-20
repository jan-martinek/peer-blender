<?php

namespace Model\Ontology;

use Symfony\Component\Yaml\Yaml;
use Model\Repository\CourseRepository;
use Model\Repository\UnitRepository;
use Model\Repository\AssignmentRepository;
use Model\Repository\QuestionRepository;
use Model\Entity\Entity;
use Model\Entity\Course;
use Model\Entity\Unit;
use Model\Entity\Assignment;
use Model\Entity\Question;
use Model\Ontology\CourseLoader;


class CourseFactory
{
    use \Nette\SmartObject;

	/** @var Path to the directory where Course definitions are stored */
	private $path;
	
	/** @var Yaml parser */
	private $yaml;
	
	/** @var Course definitions */
	private $courses = array();
	
	/** @var \Model\Repository\CourseRepository */
	public $courseRepository;
	
	/** @var \Model\Repository\UnitRepository */
	public $unitRepository;
	
	/** @var \Model\Repository\AssignmentRepository */
	public $assignmentRepository;
	
	/** @var \Model\Repository\QuestionRepository */
	public $questionRepository;
	
	/** 
	 * @param string Directory where course definitions are stored
	 */
	public function __construct(
		$path,
		CourseRepository $courseRepository,
		UnitRepository $unitRepository,
		AssignmentRepository $assignmentRepository,
		QuestionRepository $questionRepository
	) 
	{
		$this->path = $path;
		$this->yaml = new Yaml;
		
		$this->courseRepository = $courseRepository;
		$this->unitRepository = $unitRepository;
		$this->assignmentRepository = $assignmentRepository;
		$this->questionRepository = $questionRepository;
	}
	
	
	/**
	 * Returns a course definition.
	 * @param string
	 * @return CourseDefinition|FALSE
	 */
	public function get($courseName)
	{
		if (!isset($this->courses[$courseName])) {
			$data = $this->init($courseName);
		}
			
		return $this->courses[$courseName];
	}
	
	/**
	 * Inits course definition creation.
	 * @param string
	 * @return CourseDefinition|FALSE
	 */
	public function init($courseName)
	{
		$loader = new CourseLoader($courseName, $this->path);
		$course = $loader->loadYaml('course.yml');
		$units = $loader->loadUnits($course);
		$data = array('course' => $course, 'units' => $units);
		$this->courses[$courseName] = new CourseDefinition($data, $this);
	}
	
	 
	public function assembleCourse($dir)
	{
		throw new \Exception('Not implemented.');
	}
	
	public function assembleUnit(Course $course) 
	{
		throw new \Exception('Not implemented.');
	}
	
	public function assembleAssignment(Unit $unit)
	{
		$course = $unit->course;
		return $this->get($course->dir)->assembleAssignment($unit);
	}
	
	public function assembleQuestion(Assignment $assignment) 
	{
		throw new \Exception('Questions are assembled always 
			in a context of an Assignment.');
	}
	
	
	public function produceMultiple($entities)
	{
		$products = array();
		foreach ($entities as $entity) {
			$products[] = $this->produce($entity);
		}
		return $products;
	}
	
	
	public function produce(Entity $entity)
	{	
		$classname = get_class($entity);
		
		switch ($classname) {
			case "Model\Entity\Course":
				return $this->produceCourse($entity);
			case "Model\Entity\Unit":
				return $this->produceUnit($entity);
			case "Model\Entity\Assignment":
				return $this->produceAssignment($entity);
			case "Model\Entity\Question":
				return $this->produceItem($entity);
			default:
				throw new \Exception('"' . $classname . '" cannot be produced.');
		}
	}
	
	public function produceCourse(Course $course)
	{
		return $this->get($course->dir)->produce($course);
	}
	
	public function produceUnit(Unit $unit) 
	{
		$course = $unit->course;
		return $this->get($course->dir)->produceUnit($unit);
	}
	
	public function produceAssignment(Assignment $assignment)
	{
		$course = $assignment->unit->course;
		return $this->get($course->dir)->produceAssignment($assignment);
	}
	
	public function produceItem($item)
	{
		$course = $item->assignment->unit->course;
		return $this->get($course->dir)->produceItem($item);
	}
	
}

class CourseDefinitionNotFoundException extends \Exception
{
}
