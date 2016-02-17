<?php

namespace Model\Ontology;

class UnitDefinition extends \Nette\Object implements IDefinition
{   
    /** @var CourseFactory */
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
        
        $params = array('name', 'goals', 'reading', 'preface');
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
            $this->rubrics = $data['rubrics'];    
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
