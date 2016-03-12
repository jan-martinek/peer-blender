<?php

namespace Model\Ontology;

class UnitDefinition extends \Nette\Object implements IDefinition
{   
    /** @var CourseFactory */
    private $factory;
    
    
    /** @var string Unit name */
    private $name;
    
    /** @var string Unit summary */
    private $summary = '';
    
    /** @var string Unit goals */
    private $goals = '';
    
    /** @var string Assigned reading */
    private $reading = '';
    
    /** @var string Assignment preface */
    private $preface = '';
    
    /** @var array Assessment rubrics */
    private $rubrics = array(); 
    
    
    /** @var AssignmentDefinition */
    private $assignment;

    
    /**
     * Creates question definition
     * @param array
     * @param CourseDefinition 
     */
    public function __construct($data, $factory) 
    {       
        $this->factory = $factory;
        $this->defineUnit(array_shift($data));
        $this->assignment = new AssignmentDefinition($data, $this->factory);      
    }
    
    
    /**
     * Defines unit parameters.
     * @param array
     */
    private function defineUnit($data)
    {
        if (!isset($data['name'])) {
            throw new InvalidQuestionDefinitionException('Missing unit name.');
            return;
        }
        
        $params = array('name', 'summary', 'goals', 'reading', 'preface');
        foreach ($params as $param) {
            if (isset($data[$param])) {
                $this->$param = $data[$param];   
            }
        }
        
        // set rubrics
        if (!isset($data['rubrics']) || !is_array($data['rubrics'])) {
            throw new InvalidQuestionDefinitionException(
                'Rubrics are not well-formed (unit ' . $data['name'] . ').');
            return;
        } else {
            $this->defineRubrics($data['rubrics']);
        }   
    }
    
    /**
     * Defines unit's rubrics and comments
     * @param array
     */
    private function defineRubrics($rubrics) 
    {
        $hasCustomRubrics = FALSE;
        
        foreach ($rubrics as $rubric) {
            if (is_string($rubric)) 
            {
                $this->rubrics[] = new Comment($rubric);
            } 
            elseif (is_array($rubric)) 
            {
                $metric = $rubric['metric'];
                unset($rubric['metric']);
                
                $this->rubrics[] = new Rubric($metric, $rubric);
                $hasCustomRubrics = TRUE;
            } 
            else 
            {
                throw new InvalidQuestionDefinitionException(
                   'Rubrics may be defined only as a string or array.');
            }
        }
        
        if (!$hasCustomRubrics) {
            $this->rubrics[] = new DefaultRubric;
        }
    }
    
    /**
     * Assembles a new unit.
     * @return Model\Entity\Assignment
     */
    public function assemble()
    {
        throw new \Exception('Not implemented.');
    }
    
    /**
     * Produces a unit from saved entity.
     * @param Model\Entity\Unit
     * @return stdClass
     */
    public function produce($entity)
    {
        $product = new UnitProduct($entity);
        
        $product->summary = $this->summary ? $this->summary : $this->goals;
        $product->goals = $this->goals;
        $product->reading = $this->reading;
        $product->preface = $this->preface;
        $product->rubrics = $this->rubrics;
        
        $product->name = $entity->name ? $entity->name : $this->name;
        
        $product->published_since = $entity->published_since;
        $product->reviews_since = $entity->reviews_since;
        $product->objections_since = $entity->objections_since;
        $product->finalized_since = $entity->finalized_since;
        
        return $product;
    }
    
    
    public function assembleAssignment($unit)
    {
        $assignment = $this->assignment->assemble();
        $assignment->unit = $unit;
        $assignment->rubrics = $this->rubrics;
        $this->factory->assignmentRepository->persist($assignment);
        return $assignment;
    }
    
    public function produceAssignment($assignment)
    {
        return $this->assignment->produce($assignment);
    }
    
    public function produceQuestion($question)
    {
        return $this->assignment->produceQuestion($question);
    }
}

class Rubric extends \Nette\Object 
{
    private $metric;
    private $scale;
    
    public function __construct($metric, $scale)
    {
        $this->metric = $metric;
        $this->scale = $scale;
    }
    
    public function getMetric()
    {
        return $this->metric;
    }
    
    public function getScale()
    {
        return $this->scale;
    }
}

class DefaultRubric extends Rubric 
{    
    public function __construct()
    {
    }
}

class Comment extends \Nette\Object
{
    private $instructions;
    
    public function __construct($instructions)
    {
        $this->instructions = $instructions;
    }
    
    public function getInstructions()
    {
        return $this->instructions;
    }
}
