<?php

namespace Model\Ontology;

use Model\Entity\Assignment;
use Model\Entity\Question;
use Model\Ontology\QuestionDefinition;
use Model\Ontology\Reading;
use DateTime;

class AssignmentDefinition implements IDefinition, \Countable
{   
    use \Nette\SmartObject;

    /** @var CourseFactory */
    private $factory;
    
    
    /** @var array Structure of an assignment */
    private $structure = array();
    
    /** @var array Definitions applied when generating an assignment */
    private $definitions = array();
    
    /**
     * Defines questions.
     * @param array
     * @param UnitDefinition 
     */
    public function __construct($data, $factory) 
    {
        $this->factory = $factory;

        foreach ($data['content'] as $doc) {
            switch ($doc['content']) {
                case 'reading':
                    $reading = new Reading($doc);
                    if (!isset($this->definitions[$reading->source])) {
                        $this->definitions[$reading->source] = $reading;    
                    }
                    
                    $this->structure[] = $reading;
                    
                    break;
                case 'question':
                    $question = new QuestionDefinition($doc, $this->factory);
                    if (!isset($this->definitions[$question->source])) {
                        $this->definitions[$question->source] = $question;    
                    }
                    
                    $count = isset($doc['count']) ? $doc['count'] : 1;
                    for ($i = 0; $i < $count; $i++) {
                        $this->structure[] = $question;
                    }
                    
                    break;
                default:
                    throw new Exception('Content type not supported');
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
        
        foreach ($this->structure as $item) {
            if ($item instanceof QuestionDefinition) {
                $question = $item->assemble(); 
                $question->assignment = $assignment;
                $this->factory->questionRepository->persist($question);
            }
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
        
        $questionPos = 0;
        foreach($this->structure as $item) {
            if ($item instanceof QuestionDefinition) {                
                $product->structure[] = $this->factory->produce($entity->questions[$questionPos]);
                $questionPos++;
            } else if ($item instanceof Reading) {
                $product->structure[] = $item;
            }
        }
        
        return $product;
    }
    
    public function produceItem($item)
    {
        if ($item instanceof Reading) return $item;
        else if ($item instanceof Question) {
            if ($item->source) {
                return $this->definitions[$item->source]->produce($item);    
            } else {
                // legacy
                $questions = array_values(array_filter($this->structure, [$this, 'isQuestion']));
                return $questions[$item->order]->produce($item);
            }
        }
    }
    
    private function isQuestion($item) 
    { 
        return $item instanceof QuestionDefinition; 
    }
}
