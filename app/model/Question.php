<?php

namespace Model\Entity;

/**
 * @property int $id
 * @property int $order
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
    private $definition;
    private $combinations;  
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
        $this->input = isset($this->definition->input) 
            ? $this->applyVars($this->definition->input) : 'plaintext';
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
        if (!$questionsMayBeRepeated) {
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
    
    public function isDefinitionValid($definition) 
    {
        // questions are set
        if (!isset($definition->questions)) {
            throw new InvalidQuestionDefinitionException(
                'No questions found.'
            );
            return false;
        }
        
        //bloom is valid
        if (   !isset($definition->bloom) 
            || !$this->isBloomValid($definition->bloom)
        ) {
            throw new InvalidQuestionDefinitionException(
                'This objective does not belong to the revised Bloom\'s taxonomy.'
            );
            return false;
        }
        
        return true;
    }
    
    /**
     * Sets the classification of the question in Revised 
     * Bloom's Taxonomy of Education Objectives
     * (Anderson, L., & Krathwohl, D. A. (2001). Taxonomy for Learning, 
     * Teaching and Assessing: A Revision of Bloom's Taxonomy of 
     * Educational Objectives. New York: Longman.)
     * @return bool
     */
    private final function isBloomValid($objective) 
    {
        return in_array($objective, array(
            'remember',
            'understand',
            'apply',
            'analyze',
            'evaluate',
            'create'
        ));
    }
    
    /**
     * Checks if selected input method is available
     * @return bool
     */   
    private function isInputValid($input)
    {
        return in_array($input, array(
            'plaintext',
            'code',
            'markdown',
            'javascript',
            'html',
            'sql',
            'css',
            'xml',
            'file'
        ));
    }

    private function setInput($input) {
        if (!$this->isInputValid($input)) {
            throw new InvalidQuestionDefinitionException(
                'This input method is not supported.'
            );
        }
        
        $this->input = $input;
    }


    /**
     * Checks whether syntax highlighting is available for the selected input method
     * @return bool
     */    
    public function isHighlightingAvailable() 
    {
        return in_array($this->input, array(
            'markdown',
            'javascript',
            'html',
            'sql',
            'css',
            'xml'
        ));
    }
    
    public function __clone()
    {
        $this->definition = clone $this->definition;
        $this->row = clone $this->row;
    }
    
}

class QuestionDefinitionChangedException extends \Exception
{
}

class InvalidQuestionDefinitionException extends \Exception
{
}
