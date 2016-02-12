<?php

namespace Model;

use Symfony\Component\Yaml\Parser;

class CourseDefinitionFactory extends \Nette\Object
{
	/** @var Dir with Course definitions */
	private $coursesDir;
	
	/** @var Yaml parser */
	private $yaml;
	
	/** @var Course definitions */
	private $courseDefinitions = array();
	
	/** 
	 * Init factory
	 * @param Directory where course definitions are stored
	 */
	public function __construct($coursesDir) 
	{
		$this->coursesDir = $coursesDir;
		$this->yaml = new Parser;
	}
	
	/**
	 * Get course definition
	 * @param string
	 * @return CourseDefinition|FALSE
	 */
	public function get($courseName)
	{
		if (!isset($this->courseDefinitions[$courseName])) {
			$data = $this->loadData($courseName);
			
			list($courseDefinition, $unitDefinitions) 
				= $this->parse($data);
			
			$this->courseDefinitions[$courseName] 
				= new CourseDefinition(
					$courseDefinition, 
					$unitDefinitions, 
					$this
				)
			;
		}
		return $this->courseDefinitions[$courseName];
	}
	
	/**
	 * Parse course data YAML
	 * @param array file contents
	 * @return array
	 */
	private function parse($data) 
	{
		// parse course data
		if (!isset($data['course'])) {
			throw new CourseDefinitionNotFoundException;
			return;
		} 
		$courseDefinition = $this->yaml->parse($data['course']);
		unset($data['course']);
		
		// parse unit data
		$unitDefinitions = array();
		foreach ($data as $name => $file) {
			$unit = array();
			$parts = preg_split('/\n---\s*\n/', $file);
			foreach ($parts as $part) {
				if (trim($part) != '') {
					$unit[] = $this->yaml->parse(trim($part));	
				}
			}
			$unitDefinitions[$name] = $unit;
		}
		
		return array($courseDefinition, $unitDefinitions);
	}
	
	/**
	 * Loads data from course files
	 * @param string
	 * @return array file contents
	 */
	private function loadData($courseName) 
	{
		$files = array();
		$dir = glob($this->coursesDir . '/' . $courseName . '/*');
		foreach ($dir as $file) {
			if (pathinfo($file, PATHINFO_EXTENSION) == 'yml') {
				$filename = pathinfo($file, PATHINFO_FILENAME);
				$files[$filename] = file_get_contents($file);    
			}
		}
		return $files;
	}
}

class CourseDefinitionNotFoundException extends \Exception
{
}
