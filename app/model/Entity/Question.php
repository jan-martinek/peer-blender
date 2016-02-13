<?php

namespace Model\Entity;

/**
 * @property int $id
 * @property int $order
 * @property int $definition_id
 * @property string $variant
 * @property string $text
 * @property string $input
 * @property string $prefill
 * @property int $comments
 * @property Assignment $assignment m:hasOne 
 * @property Answer|NULL $answer m:belongsToOne
 */
class Question extends Entity
{   
    private $replacements;
    
    private $definitionHash;
    private $selectedQuestion;
    private $selectedCombination;

    /**
     * Generates questions from a definition 
     * @param bool $force regenerate vars combinations even when available
     */
    public function generateFromDefinition($force = FALSE)
    {   
        if (isset($this->definition->vars) 
            && is_array($this->definition->vars)
        ) {
            if ($force || is_null($this->combinations)) {
                $this->prepVarsCombinations();  
            }
            $this->selectCombination();
            $this->prepReplacements();
            
        } else if (!is_null($this->selectedCombination)) {
            throw new InvalidQuestionDefinitionException(
                'Vars combination has been announced, 
                but no vars were found.'
            );
        }
        
        $this->selectQuestion();
        
        $this->variant = json_encode(array(
            'selectedCombination' => $this->selectedCombination,
            'selectedQuestion' => $this->selectedQuestion,
            'definitionHash' => $this->definitionHash
        ));
        
        
        $this->text = $this->applyVars(
            $this->definition->questions[$this->selectedQuestion]
        );
        $this->prefill = isset($this->definition->prefill) 
            ? $this->applyVars($this->definition->prefill) : '';
        $this->comments = isset($this->definition->comments) 
            ? $this->definition->comments : 0;
    }
    
    public function variate() 
    {
        $questionsMayBeRepeated = (
            isset($this->definition->allowRepeat) 
            && $this->definition->allowRepeat == 'question'
        );
        if (!$questionsMayBeRepeated) {
            unset($this->definition->questions[$this->selectedQuestion]);   
        }
        
        $varsMayBeRepeated = (
            isset($this->definition->allowRepeat) 
            && $this->definition->allowRepeat == 'vars'
        );
        if (!$varsMayBeRepeated) {
            unset($this->combinations[$this->selectedCombination]);   
        }
        
        $this->selectedQuestion = null;
        $this->selectedCombination = null;
        
        $this->generateFromDefinition();
        
        return $this;
    }

    /**
     * Restores variant from saved question
     * @param bool force restore even when definition is different than before
     */
    public function restoreVariant($force = FALSE) 
    {
        $variant = json_decode($this->variant);
        
        if (!$force && $this->definitionHash != $variant['definitionHash']) {
            throw new QuestionDefinitionChangedException;
        } else {
            $this->selectedCombination = $variant['selectedCombination'];
            $this->selectedQuestion = $variant['selectedQuestion']; 
            $this->definitionHash = $variant['definitionHash']; 
        }
    }

    /**
     * Enumerates all possible combinations of used variables
     */
    private function prepVarsCombinations() 
    {
        $combinations = array(array());
        
        foreach ($this->definition->vars as $key => $var) {
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
     * Gets a specific combination of variables
     * @param bool force select even already selected
     */
    private function selectCombination($force = FALSE) 
    {
        if ($force || is_null($this->selectedCombination)) {
            $this->selectedCombination = array_rand($this->combinations);  
        } elseif (!isset($this->combinations[$combination])) {
            throw new InvalidQuestionDefinitionException(
                'Selected vars combination does not exist.'
            );
        }
    }
    
    /**
     * Selects a question text
     * @param bool force select even already selected
     */
    private function selectQuestion($force = FALSE) 
    {
        if ($force || is_null($this->selectedQuestion)) {
            $this->selectedQuestion = array_rand($this->definition->questions); 
        }
    }
    
    
    /**
     * Get leaf nodes only and wrap the keys in %'s
     */
    private function prepReplacements() 
    {
        $replacements = array();
        array_walk_recursive(
            $this->combinations[$this->selectedCombination], 
            function($item, $key) use (&$replacements) {
                $replacements['%' . $key . '%'] = $item;
            }
        );
        $this->replacements = $replacements;
    }
        
    private function applyVars($text) 
    {
        if (is_array($this->replacements)) {
            return strtr($text, $this->replacements);   
        } else {
            return $text;
        }
    }

    /**
     * Checks important properties and sets the definition
     */
    public function setDefinition($definition) 
    {
        $definition = (object) $definition;
        
        if (!isset($definition->input)) {
            $definition->input = 'plaintext';   
        }
        
        if($this->isDefinitionValid($definition)) {
            $this->definition = $definition;
            
            if (is_string($this->definition->questions)) {
                $this->definition->questions = array(
                    $this->definition->questions
                );
            }
            
            $this->definitionHash = substr(
                sha1(serialize($definition)), 0, 6
            );    
        }
    }
    
    public function getDefinition() {
        return $this->definition;
    }

    
}

class QuestionDefinitionChangedException extends \Exception
{
}
