<?php

namespace Model\Ontology;

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

abstract class AbstractRubric implements IRubric
{
    use \Nette\SmartObject;

    private $questionKey = null;
    
    public function bindToQuestion($key) {
        $this->questionKey = $key;
    }
    
    public function isBoundToQuestion() {
        return is_null($this->questionKey) ? false : true;
    }
    
    public function getBoundQuestionKey() {
        return is_null($this->questionKey) ? 
            'none' : $this->questionKey;
    }
    
    
    public function calcScore() {
        throw new Exception('Not implemented.');
    }
    
    function setRaw($raw) {
        throw new Exception('Not implemented.');
    }

    function getRaw() {
        throw new Exception('Not implemented.');
    }
}

class RubricBuilder 
{
    use \Nette\SmartObject;

    public function buildSet($rubricSetData) 
    {
    	$set = array();	
    	
    	foreach($rubricSetData as $rubricData) {
    		$set[] = $this->build($rubricData);
    	}
    	
    	return $set;
    }
    
    public function build($rubricData) 
    {
        if (is_string($rubricData)) {
            return new Comment($rubricData);
        } elseif (is_array($rubricData)) {
            if (isset($rubricData['metric'])) {
                $metric = $rubricData['metric'];
                unset($rubricData['metric']);
                return new Rubric($metric, $rubricData);
            } else if (isset($rubricData['checklist'])) {
                $description = $rubricData['checklist'];
                unset($rubricData['checklist']);
                return new Checklist($description, $rubricData['items']);
            }
        } else {
            throw new InvalidQuestionDefinitionException(
               'Rubrics may be defined only as a string or array.');
        }
    }
}

/**
 * @property string $description
 * @property array $items
 * @property array $raw
 */
class Checklist extends AbstractRubric implements IRubric
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

/**
 * @property string $weight
 * @property string $metric
 */
class ChecklistItem
{
    use \Nette\SmartObject;

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

class Rubric
{
    use \Nette\SmartObject;

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

/**
 * @property string $instructions
 */
class Comment
{
    use \Nette\SmartObject;

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
