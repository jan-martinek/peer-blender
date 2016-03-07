<?php

namespace Model\Ontology;

class Params extends \Nette\Object implements \Countable
{
    /** @var Combinations @see enumerateCombinations() */
    private $combinations;
    
    /** @var bool Combination may be repeated in multiple questions */
    private $repeatAllowed = FALSE;
    
    /** @var array Used up combination keys when combinations may not be repeated */
    private $usedCombinationKeys = array();
    
    
    
    /**
     * @param array
     */
    public function __construct($data) 
    {
        $this->enumerateCombinations($data);
    }
    
    
    public function allowRepeat()
    {
        $this->repeatAllowed = TRUE;
    }

    
    /**
     * Enumerates all possible combinations of used variables
     * @param array
     */
    private function enumerateCombinations($data) 
    {
        $combinations = array(array());
        foreach ($data as $key => $var) {
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
     * @return int Returns a random combination key.
     */
    public function assemble()
    {
        $keys = array_keys($this->combinations);
        $availableKeys = array_diff($keys, $this->usedCombinationKeys);
        $key = $availableKeys[array_rand($availableKeys)];
        
        if (!$this->repeatAllowed) {
            $this->usedCombinationKeys[] = $key;
        }
        
        return $key;
    }
    
    /**
     * Returns a set of replacements for use in 
     * a strtr() function: walks over leaf nodes only 
     * and wraps the keys in %'s.
     * @param int
     * @return array
     */
    public function produce($key)
    {
        $replacements = array();
        array_walk_recursive(
            $this->combinations[$key], 
            function($item, $key) use (&$replacements) {
                $replacements['%' . $key . '%'] = $item;
            }
        );
        return $replacements;
    }
    
    /**
     * Returns all combinations' keys.
     * @return array
     */
    public function getKeys() 
    {
        return array_keys($this->combinations);
    }
    
    /**
     * Returns all possible values of a specific variable.
     * @param string
     * @return array
     */
    public function getValues($var) 
    {
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
        
    
    /**
     * Get combinations' count
     * @return int
     */
    public function count() 
    {
        return count($this->combinations);
    }  
}
