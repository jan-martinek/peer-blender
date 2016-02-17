<?php

namespace Model\Ontology;

use Model\Entity\Assignment;
use DateTime;

class AssignmentDefinition extends \Nette\Object implements IDefinition, \Countable
{   
    /** @var CourseFactory */
    private $factory;
    
    
    /** @var array Structure applied when generating an assignment */
    private $structure = array();
    
    
    /**
     * Defines questions.
     * @param array
     * @param UnitDefinition 
     */
    public function __construct($data, $factory) 
    {
        $this->factory = $factory;
        foreach ($data as $doc) {
            $question = new QuestionDefinition($doc, $this->factory);
            
            $count = isset($doc['count']) ? $doc['count'] : 1;
            for ($i = 0; $i < $count; $i++) {
                $this->structure[] = $question;
            }
        }
    }
    
    
    /**
     * Returns count of the questions in the assignment
     * @return int
     */
    public function count()
    {
        return count($this->structure);
    }
    
    
    /**
     * Assembles a new assignment.
     * @return Model\Entity\Assignment
     */
    public function assemble()
    {
        $assignment = new Assignment;
        $assignment->generated_at = new DateTime;
        $this->factory->assignmentRepository->persist($assignment);
        
        foreach ($this->structure as $order => $questionDefinition) {
            $question = $questionDefinition->assemble(); 
            $question->assignment = $assignment;
            $question->order = $order;
            $this->factory->questionRepository->persist($question);
        }
        
        return $assignment;
    }
    
    /**
     * Produces an assignment from saved entity.
     * @param Model\Entity\Assignment
     * @return Model\Ontology\AssignmentProduct
     */
    public function produce($entity)
    {
        $product = new AssignmentProduct($entity);
        $product->generated_at = $entity->generated_at;
        
        foreach ($entity->questions as $question) {
            $product->questions[] = $this->factory->produce($question);
        }
        
        return $product;
    }
    
    
    public function produceQuestion($question)
    {
        $product = $this->structure[$question->order]->produce($question);
        $product->order = $question->order;
        return $product;
    }
}
