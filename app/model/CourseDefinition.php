<?php

namespace Model;

use Symfony\Component\Yaml\Parser;

class CourseDefinition extends \Nette\Object
{
	/** @var CourseDefinitionFactory */
	private $factory;
	
	
	/** @var string Author of the course */
	private $author;
	
	/** @var string Course name */
	private $name;
	
	/** @var string Course goals */
	private $goals;
	
	/** @var string Course methods */
	private $methods;
	
	/** @var string Course support info */
	private $support;
	
	/** @var array Course support info */
	private $units = array();
	
	/** Creates course definition
	 * @param array
	 * @param array
	 * @param CourseDefinitionFactory
	 */
	public function __construct($definition, $unitsDefinition, CourseDefinitionFactory $factory) 
	{	
		$this->factory = $factory;
		
		$params = array('author', 'name', 'goals', 'methods', 'support');
		foreach ($params as $param) {
			if (isset($definition[$param])) {
				$this->$param = $definition[$param];	
			}
		}
		
		foreach ($unitsDefinition as $unit) {			
			$this->units[] = new UnitDefinition($unit, $this->factory);
		}	
	}
}



