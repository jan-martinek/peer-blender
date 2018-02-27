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
