<?php

namespace Model\Ontology;

class CourseDefinition implements IDefinition
{
	use \Nette\SmartObject;

	/** @var CourseFactory */
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
	
	/** @var string Course custom menu items */
	private $menu;
	
	/** @var string Course custom calendar items */
	private $calendar;
	
	
	/** @var array */
	private $units = array();
	
	/** 
	 * Creates course definition
	 * @param array
	 * @param array
	 * @param CourseFactory
	 */
	public function __construct($data, $factory) 
	{	
		$this->factory = $factory;
		$params = array('author', 'name', 'goals', 'methods', 'support', 'menu', 'calendar');
		foreach ($params as $param) {
			if (isset($data['course'][$param])) {
				$this->$param = $data['course'][$param];	
			}
		}
		
		foreach ($data['units'] as $name => $unitData) {			
			$this->units[$name] = new UnitDefinition($unitData, $this->factory);
		}	
	}
	
	/**
	 * Assembles a new course.
	 */
	public function assemble()
	{
	    throw new \Exception('Not implemented.');
	}
	
	/**
	 * Produces a course from saved entity.
	 * @param Model\Entity\Course
	 * @return stdClass
	 */
	public function produce($entity)
	{
		$product = new CourseProduct($entity);
		
		$product->author = $this->author;
		$product->goals = $this->goals;
		$product->methods = $this->methods;
		$product->support = $this->support;
		$product->menu = $this->menu;
		$product->calendar = $this->calendar;
		
		$product->name = $entity->name ? $entity->name : $this->name;
		
		$product->reviewCount = $entity->reviewCount;
		$product->uploadMaxFilesizeKb = $entity->uploadMaxFilesizeKb;
		$product->gaCode = $entity->gaCode;
		
		return $product;
	}
	
	public function assembleAssignment($unit) 
	{
		return $this->units[$unit->def]->assembleAssignment($unit);
	}
	
	
	public function produceUnit($unit)
	{
		if (!isset($this->units[$unit->def])) {
			throw new UnitDefinitionNotFound('Unit named "' . $unit->def . '" not found.');
			return;
		}
		return $this->units[$unit->def]->produce($unit);
	}
	
	public function produceAssignment($assignment)
	{
		$unit = $assignment->unit;
		return $this->units[$unit->def]->produceAssignment($assignment);
	}
	
	public function produceItem($item)
	{
		$unit = $item->assignment->unit;
		return $this->units[$unit->def]->produceItem($item);
	}
}

class UnitDefinitionNotFound extends \Exception
{
}
