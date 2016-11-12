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
                if (isset($rubric['metric'])) {
                    $metric = $rubric['metric'];
                    unset($rubric['metric']);
                    
                    $this->rubrics[] = new Rubric($metric, $rubric);
                    $hasCustomRubrics = TRUE;
                } else if (isset($rubric['checklist'])) {
                    $description = $rubric['checklist'];
                    unset($rubric['checklist']);
                    
                    $this->rubrics[] = new Checklist($description, $rubric['items']);
                    $hasCustomRubrics = TRUE;
                }
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
        $this->factory->assignmentRepository->persist($assignment);
        return $assignment;
    }
    
    public function produceAssignment($assignment)
    {
        
        $assignment = $this->assignment->produce($assignment);
        $assignment->rubrics = $this->rubrics;
        return $assignment;
    }
    
    public function produceQuestion($question)
    {
        return $this->assignment->produceQuestion($question);
    }
}

interface IRubric
{   
    /**
     * Returns score calculated from 
     * a stored raw value
     * @return int score >= 0.00 && score <= 3.00, rounded to two decimals
     */
    function calcScore();
    
    /**
     * Sets raw value from which 
     * the input state is recoverable
     * @param string
     */
    function setRaw($raw);
    
    /**
     * Returns raw value from which 
     * the input state is recoverable 
     * @return string
     */
    function getRaw();
}

class Checklist extends \Nette\Object implements IRubric
{
    private $description;
    private $items = array();
    private $raw;
    private $totalWeight = 0; 
    private $maxScore = 3;
    
    /**
     * @param string
     * @param array Array of strings.
     */
    public function __construct($description, $items) {
        $this->description = $description;
        
        foreach ($items as $item) {
            $itemObj = new ChecklistItem($item);
            $this->totalWeight += $itemObj->weight;
            $this->items[] = $itemObj;
        }
    }
    
    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * @return array Returns array of ChecklistItem objects.
     */
    public function getItems() {
        return $this->items;
    }
    
    public function calcScore()
    {
        $score = 0;
        if (is_null($this->raw)) {
            return NULL;
        }
        
        foreach ($this->raw as $checkedItem) {
            $score += $this->items[$checkedItem]->weight;
        }
        
        $adjustedScore = $this->maxScore / $this->totalWeight * $score;
        return round($adjustedScore * 100)/100;
    }
    
    /**
     * Sets a raw value
     * @param string Array of checked items, eg. [0, 1, 4, 5]
     */
    public function setRaw($raw)
    {
        $this->raw = $raw;
    }
    
    public function getRaw($strict = FALSE)
    {
        if (is_null($this->raw)) {
            if ($strict) {
                throw new \Exception('Raw value hasn\'t been set.');    
            } else {
                return array();
            }
        } else {
            return $this->raw;
        }
    }
}

class ChecklistItem extends \Nette\Object
{
    private $metric;
    private $weight = 1;
    
    /**
     * Parses itemString, if it contains a weight
     * information (defined as space, lowercase "w" 
     * and a real number, such as "The file is 
     * included. w2" or "Script runs. w0.5") it is
     * stored
     * @param string
     */
    public function __construct($itemString) {
        if (preg_match("/^(.+) w([0-9.]+)$/", $itemString, $matches)) {
            $this->metric = $matches[1];
            $this->weight = $matches[2];
        } else {
            $this->metric = $itemString;
        }
    }
    
    public function getMetric()
    {
        return $this->metric;
    }
    
    public function getWeight()
    {
        return $this->weight;
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
