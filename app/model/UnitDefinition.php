<?php

namespace Model;

class UnitDefinition extends \Nette\Object
{	
	/** @var CourseDefinitionFactory */
	private $factory;
	
	
	/** @var string Unit name */
	private $name;
	
	/** @var string Unit goals */
	private $goals = '';
	
	/** @var string Assigned reading */
	private $reading = '';
	
	/** @var string Assignment preface */
	private $preface = '';
	
	/** @var array Assessment rubrics */
	private $rubrics = array();	
	
	
	/** @var array Array of VarsCombinationStorages */
	private $combinations = array();
	
	/** @var array Question definitions */
	private $questions = array();
	
	/** @var array Structure applied when generating an assignment 
     *
     * $example = array(
     *    array(1),
     *    array(2),
     *    array(3, 4),
     *    array(5),
     *    array(6),
     *    array(6),
     *    array(7, 8, 9)
     * );
     *
     */
	private $structure = array();

	
	/**
	 * Creates question definition
	 * @param array $definition
	 * @param CourseDefinitionFactory $factory
	 */
	public function __construct($definition, CourseDefinitionFactory $factory) 
	{		
		$this->factory = $factory;
		
		// break apart unit definition
		$unitDef = $definition[0];
		unset($definition[0]);
		$questionDefs = $definition;
		
		// set params
		$params = array('name', 'goals', 'reading', 'preface');
		foreach ($params as $param) {
			if (isset($unitDef[$param])) {
				$this->$param = $unitDef[$param];	
			}
		}
		
		// set rubrics
		if (isset($unitDef->rubrics) && is_array($unitDef->rubrics)) {
			$this->rubrics = $unitDef->rubrics;
		}
		
		// set questions
		foreach ($questionDefs as $questionDef) {	
			$ids = $this->produceQuestionDefinitions($questionDef);
			$count = isset($questionDef->count) ? $questionDef->count : 1;
			for ($i = 0; $i < $count; $i++) {
				$this->structure[] = $ids;
			}				
		}
	}
	
	
	/**
	 * Creates questions and adds them to the assignment structure 
	 * @param array question definition
	 * @param array parent definition for param inheritance
	 */
	private function produceQuestionDefinitions($definition, $inherit = array()) 
	{
		// inherit from parent
		if (count($inherit)) {
			$definition = array_merge($inherit, $definition);
		}
		
		// var combinations
		$varsAvailable = (
			isset($definition['vars']) 
			&& is_array($definition['vars'])
		);
		$combination = $varsAvailable 
			? new VarsCombinationStorage($definition['vars']) 
			: null;
			
		// questions creation
		if (!isset($definition['questions'])) {
		    throw new InvalidQuestionDefinitionException(
		        'No questions found.'
		    );
		    return;
		} else if (is_string($definition['questions'])) {
			$definition['questions'] = array($definition['questions']);
		}
		
		$ids = array();
		foreach ($definition['questions'] as $question) 
		{
			if (is_string($question)) {
				$this->questions[] = new QuestionDefinition(
					$question,
					$definition, 
					$combination, 
					$this->factory
				);
				$ids[] = max(array_keys($this->questions));
			} else {
				$ids = array_merge(
					$ids, 
					$this->produceQuestionDefinitions($question, $definition)
				);
			}
		}
		return $ids;
	}
}

class VarsCombinationStorage extends \Nette\Object
{
	
	/** @var Combinations @see produceVarsCombinations() */
	private $combinations;
	
	public function __construct($vars) {
		$this->produceVarsCombinations($vars);
		return $this;
	}
	
	/**
	 * Enumerates all possible combinations of used variables
	 * @param array
	 */
	private function produceVarsCombinations($vars) 
	{
	    $combinations = array(array());
	    
	    foreach ($vars as $key => $var) {
	        $combination = array();
	        foreach ($combinations as $values) {
	            foreach ($var as $val) {
	                $combination[] = array_merge($values, array($key => $val));
	            }
	        }
	        $combinations = $combination;
	    }
	    
	    $this->combinations = $combinations;
	}
	
	/**
	 * Get specific variables combinations
	 * @param int
	 * @return array
	 */
	public function getCombination($key) {
		return $this->combinations[$key];
	}
	
	/**
	 * Get all combinations' keys
	 * @return array
	 */
	public function getKeys() {
		return array_keys($this->combinations);
	}
	
	/**
	 * Get all var values
	 * @param string
	 */
	public function getValues($var) {
		$values = array();
		
		foreach ($this->combinations as $combination) {
			
			$flat = array();
			array_walk_recursive(
			    $combination, 
			    function($item, $key) use (&$flat) {
			        $flat[$key] = $item;
			    }
			);
			
			if (!in_array($flat[$var], $values)) {
				$values[] = $flat[$var];
			}
		}
		
		return $values;
	}
}
