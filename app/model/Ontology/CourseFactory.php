<?php

namespace Model\Ontology;

use Symfony\Component\Yaml\Parser;
use Model\Repository\CourseRepository;
use Model\Repository\UnitRepository;
use Model\Repository\AssignmentRepository;
use Model\Repository\QuestionRepository;
use Model\Entity\Entity;
use Model\Entity\Course;
use Model\Entity\Unit;
use Model\Entity\Assignment;
use Model\Entity\Question;


class CourseFactory extends \Nette\Object
{
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
		$this->yaml = new Parser;
		
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
		$resources = $this->fetchResources($courseName);
		$data = array(
			'course' => $this->parseCourseResources($resources), 
			'units' => $this->parseUnitResources($resources)
		);		
		$this->courses[$courseName] = new CourseDefinition($data, $this);
	}	
	
	/**
	 * Fetches resources stored in path/courseName.
	 * @param string
	 * @return array source contents
	 */
	private function fetchResources($courseName) 
	{
		$resources = array();
		$dir = glob($this->path . '/' . $courseName . '/*');
		foreach ($dir as $file) {
			if (pathinfo($file, PATHINFO_EXTENSION) == 'yml') {
				$name = pathinfo($file, PATHINFO_FILENAME);
				$resources[$name] = file_get_contents($file);    
			}
		}
		return $resources;
	}
	
	/**
	 * Parses course's YAML source.
	 * @param array file contents
	 * @return array
	 */
	private function parseCourseResources($resources) 
	{
		if (!isset($resources['course'])) {
			throw new CourseDefinitionNotFoundException;
			return;
		} 
		return $this->yaml->parse($resources['course']);
	}
	
	/**
	 * Parses all units' YAML source.
	 * @param array file contents
	 * @return array units data
	 */
	private function parseUnitResources($resources) 
	{
		$data = array();
		foreach ($resources as $name => $source) {
			if ($name === 'course') {
				continue;
			}
			
			$unit = array();
			$yamlDocs = preg_split('/\n---\s*\n/', $source);
			foreach ($yamlDocs as $yamlDoc) {
				if (trim($yamlDoc) != '') {
					$unit[] = $this->yaml->parse(trim($yamlDoc));	
				}
			}
			$data[$name] = $unit;
		}
		return $data;
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
				return $this->produceQuestion($entity);
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
	
	public function produceQuestion(Question $question) 
	{
		$course = $question->assignment->unit->course;
		return $this->get($course->dir)->produceQuestion($question);
	}
	
}

class CourseDefinitionNotFoundException extends \Exception
{
}
