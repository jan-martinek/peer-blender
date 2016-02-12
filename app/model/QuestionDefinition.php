<?php

namespace Model;

class QuestionDefinition extends \Nette\Object
{   
    /** @var CourseDefinitionFactory */
    private $factory;
    
    /** @var Classification of the question in 
     * Revised Bloom's Taxonomy of Education Objectives 
     * (Anderson, L., & Krathwohl, D. A. (2001). Taxonomy 
     * for Learning, Teaching and Assessing: A Revision 
     * of Bloom's Taxonomy of Educational Objectives. 
     * New York: Longman.) @see isBloomValid()
     */
    private $bloom;
    
    /** @var string Input method */
    private $input = 'plaintext';
    
    /** @var string Prefill value */
    private $prefill = '';
    
    /** @var int|FALSE Comment rows */
    private $comments = FALSE;
    
    /** @var string Question */
    private $question;
    
    /** @var VarsCombinationStorage */
    private $combinations = null;
    
    /** @var string Hash of definition for consistency checking */
    private $definitionHash;
    
    /**
     * Generates question definition
     * @param string
     * @param array
     * @param VarsCombinationStorage|NULL
     * @param CourseDefinitionFactory
     */
    public function __construct($question, $definition, VarsCombinationStorage $combinations = null, CourseDefinitionFactory $factory)
    {
        $this->question = $question;
        $this->definitionHash = substr(sha1(serialize($definition)), 0, 6);   
        $this->combinations = $combinations;
        $this->factory = $factory;
        
        // set bloom
        $bloomIsInvalid = (!isset($definition['bloom']) 
            || !$this->isBloomValid($definition['bloom']));
        if ($bloomIsInvalid) {
            throw new InvalidQuestionDefinitionException('This objective does not belong to the revised Bloom\'s taxonomy.');
            return;
        }
        $this->bloom = $definition['bloom'];
        
        // set input
        if (isset($definition['input'])) {
            if (!$this->isInputValid($definition['input'])) {
                throw new InvalidQuestionDefinitionException(
                    'This input method is not supported.'
                );
            }
        }
        
        // set prefill
        if (isset($definition['prefill'])) {
            $this->prefill = $definition['prefill'];
        }
        
        // set comments
        if (isset($definition['prefill'])) {
            $this->prefill = $definition['prefill'];
        }
    }
    
    /**
     * Checks the bloom classification
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
     * Checks if selected input method is available,
     * if input is defined as a variable, all values are checked
     * @return bool
     */   
    private function isInputValid($input)
    {        
        $validInputMethods = array(
            'plaintext',
            'code',
            'markdown',
            'javascript',
            'html',
            'sql',
            'css',
            'xml',
            'file'
        );
        
        // check var values if defined as %variable%
        if (preg_match("/^%(.+)%$/", $input, $match)) {
            foreach ($this->combinations->getValues($match[1]) as $value) {
                if (!in_array($value, $validInputMethods)) {
                    throw new InvalidQuestionDefinitionException('At least one of the possible values of the %' . $input . '% variable  is not a valid input method (value: "' . $value . '").');
                    return FALSE;
                }
            }
        } else if (!in_array($input, $validInputMethods)) {
            throw new InvalidQuestionDefinitionException('"' . $input . ' is not valid input method.');
            return FALSE;
        }
        
        return TRUE;
    }

    /**
     * Checks whether syntax highlighting is 
     * available for the selected input method
     * @return bool
     */    
    public function isHighlightingAvailable() 
    {
        return in_array($this->definition->input, array(
            'markdown',
            'javascript',
            'html',
            'sql',
            'css',
            'xml'
        ));
    }
}

class InvalidQuestionDefinitionException extends \Exception
{
}
