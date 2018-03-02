<?php

namespace Model\Ontology;

use Symfony\Component\Yaml\Parser;

class CourseLoader extends \Nette\Object
{
	/** @var Path to the directory where Course definition is stored */
	private $path;
	
	/** @var Yaml parser */
	private $yaml;
	
	/** 
	 * @param string Directory where course definitions are stored
	 */
	public function __construct($courseName, $courseDirPath) 
	{
		$this->path = $courseDirPath . '/' . $courseName;
		$this->yaml = new Parser;
	}
	
	public function loadUnits($course) 
	{
		if (!isset($course['units'])) {
			$legacyLoader = new LegacyUnitLoader($this->path, $this->yaml);
			return $legacyLoader->loadUnits();
		}

		$units = array();

		foreach ($course['units'] as $unitName) {
			$unit = $this->loadYaml($unitName . '/_unit.yml');
			$unit['name'] = $unitName;

			if (isset($unit['outline'])) $unit['content'] = 
				$this->loadContent($unitName, $unit['outline']);

			$units[$unitName] = $unit;
		}

		return $units;
	}

	private function loadContent($unitName, $outline) 
	{
		$content = array();

		foreach ($outline as $item) {
			$filename = is_array($item) ? $item['filename'] : $item;
			
			$itemContent = preg_match('/\.yml$/', $filename)
				? $this->loadQuestion($unitName, $filename)
				: $this->loadReading($unitName, $filename);

			if (is_array($item)) $itemContent = array_merge($itemContent, $item);
			
			$content[] = $itemContent;
		}
		return $content;
	}

	private function loadQuestion($unitName, $filename) 
	{
		$content = $this->loadYaml($unitName . '/' . $filename);
		$content['content'] = 'question';
		$content['filename'] = $filename;
		return $content;
	}

	private function loadReading($unitName, $filename) 
	{
		return array(
			'content' => 'reading',
			'filename' => $filename,
			'data' => $this->loadFile($unitName . '/' . $filename)
		);
	}

	public function loadYaml($name) 
	{
		return $this->yaml->parse($this->loadFile($name));
	}

	public function loadFile($name) 
	{
		return file_get_contents($this->path . '/' . $name);
	}
}

class LegacyUnitLoader {
	private $path;
	private $yaml;

	public function __construct($path, $yaml)
	{
		$this->path = $path;
		$this->yaml = $yaml;
	}

	public function loadUnits() {
		$resources = $this->fetchResources();
		return $this->parseUnitResources($resources);
	}

	/**
	 * Fetches resources stored in path/courseName.
	 * @param string
	 * @return array source contents
	 */
	private function fetchResources() 
	{
		$resources = array();
		$dir = glob($this->path . '/*');
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
			
			$unitData = array();
			$yamlDocs = preg_split('/\n---\s*\n/', $source);
			foreach ($yamlDocs as $i => $yamlDoc) {
				if (trim($yamlDoc) != '') {
					$unitData[] = $this->yaml->parse(trim($yamlDoc));
				}
				if ($i > 0) {
					$unitData[count($unitData) - 1]['content'] = 'question';
					$unitData[count($unitData) - 1]['filename'] = $i - 1;
				}
			}

			$unit = array_shift($unitData);
			$unit['content'] = $unitData;
			$unit['title'] = $unit['name'];
			$unit['name'] = $name;
			$data[$name] = $unit;
		}
		return $data;
	}
}
