<?php

namespace Model;

use Symfony\Component\Yaml\Parser;

class CourseDefinition extends \Nette\Object
{
	const QUESTIONS = 'questions';
	
	/** @var Dir with Course definitions */
	private $dir;
	
	/** @var Yaml parser */
	private $yaml;
	
	
	public function __construct($dir) 
	{
		$this->dir = $dir;
		$this->yaml = new Parser;
	} 
	
	public function init($name) 
	{
		if (file_exists($this->dir . '/' . $name . '/course.yml')) {
			$dir = glob($this->dir . '/' . $name . '/*');
			foreach ($dir as $file) {
				if (pathinfo($file, PATHINFO_EXTENSION) == 'yml') {
					$this->files[pathinfo($file, PATHINFO_FILENAME)] = file_get_contents($file);    
				}
			}
		} else {
			throw new CourseDefinitionNotFoundException;
		}
	}
	
	public function get($object, $param = null) 
	{
		if ($object instanceof \Model\Entity\Course) {
			$file = $this->dir . '/' . $object->dir . '/course.yml';
			$definition = file_get_contents($file);
			return (object) $this->yaml->parse($definition);	
		} else if ($object instanceof \Model\Entity\Unit) {
			$course = $object->course;
			$file = $this->dir . '/' . $course->dir . '/' . $object->def . '.yml';
			$definitions = preg_split('/\n---\s*\n/', file_get_contents($file));
			
			if ($param === self::QUESTIONS) {
				unset($definitions[0]);
				$questions = array();
				foreach ($definitions as $i => $question) {
					$questions[$i] = $this->yaml->parse($question);
				}
				return $questions;
			} else {
				return (object) $this->yaml->parse($definitions[0]);		
			}
		}
	}
}

class CourseDefinitionNotFoundException extends \Exception
{
}
