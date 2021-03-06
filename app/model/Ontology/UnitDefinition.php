<?php

namespace Model\Ontology;

class UnitDefinition implements IDefinition
{   
    use \Nette\SmartObject;

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
    
    /** @var array Assessment tags */
    private $tags = array(); 
    
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

        if (isset($data['content'])) {
            $this->assignment = new AssignmentDefinition($data, $this->factory);
            $this->defineUnit($data);
        }
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
        
        $params = array('name', 'summary', 'goals', 'reading', 'preface', 'tags');
        foreach ($params as $param) {
            if (isset($data[$param])) {
                $this->$param = $data[$param];   
            }
        }
        
        $this->defineRubrics($data['rubrics']);
    }
    
    /**
     * Defines unit's rubrics and comments
     * @param array
     */
    private function defineRubrics($rubrics) 
    {
        $builder = new RubricBuilder;
        $this->rubrics = $builder->buildSet($rubrics);
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
        $product->tags = $this->tags;
        
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
        
        $assignment = $this->assignment->produce($assignment);
        $assignment->rubrics = $this->rubrics;
        return $assignment;
    }
    
    public function produceItem($item)
    {
        return $this->assignment->produceItem($item);
    }
}
