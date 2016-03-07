<?php

namespace Model\Ontology;

class QuestionItem extends \Nette\Object
{   
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
    private $text;
    
    /** @var string Hash of definition for consistency checking */
    private $hash;
    
    /** @var Params */
    private $params;
    
    
    /**
     * Defines a question item (a special case of a question).
     * @param string
     * @param array
     * @param QuestionDefinition parent question definition
     * @param Params
     */
    public function __construct($data, $text, $params)
    {
        $this->text = $text;
        $this->params = $params;
        $this->hash = substr(sha1(serialize($data)), 0, 6);
        
        $this->setBloom($data);
        $this->setInput($data);
        
        // set prefill
        if (isset($data['prefill'])) {
            $this->prefill = $data['prefill'];
        }
        
        // set comments
        if (isset($data['comments'])) {
            $this->comments = $data['comments'];
        }
    }
    
    
    /**
     * @param array
     */
    private function setBloom($data)
    {
        $bloomIsInvalid = (!isset($data['bloom']) && isset($data['questions']))
            || !$this->isBloomValid($data['bloom']);
        if ($bloomIsInvalid) {
            throw new InvalidQuestionDefinitionException('This objective ("'.$data['bloom'].'"") does not belong to the revised Bloom\'s taxonomy.');
        }
        $this->bloom = $data['bloom'];
    }
    
    
    /**
     * @param array
     */
    private function setInput($data)
    {
        if (isset($data['input'])) {
            if ($this->isInputValid($data['input'])) {
                $this->input = $data['input'];
            } else {
                throw new InvalidQuestionDefinitionException(
                    'This input method is not supported.'
                );
            }
        }
    }
    
    
    /**
     * Checks the Bloom classification @see $bloom.
     * @param string
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
     * Checks if selected input method is available.
     * If input is defined as a variable, all values are checked.
     * @param string
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
            $varName = $match[1];
            $values = $this->params->getValues($varName);
            
            foreach ($values as $value) {
                if (!in_array($value, $validInputMethods)) {
                    throw new InvalidQuestionDefinitionException('At least one of the possible values of the %' . $input . '% variable is not a valid input method (invalid value: "' . $value . '").');
                    return FALSE;
                }
            }
        } else if (!in_array($input, $validInputMethods)) {
            throw new InvalidQuestionDefinitionException('"' . $input . ' is not a valid input method.');
            return FALSE;
        }
        
        return TRUE;
    }


    public function hasParams()
    {
        return $this->params ? TRUE : FALSE;
    }
    
    public function assembleParamsKey()
    {
        if ($this->hasParams()) {
            return $this->params->assemble();
        } else {
            return null;
        }
    }

    public function applyParams($text, $key) 
    {
        if ($this->params) {
            return strtr($text, $this->params->produce($key));
        } else {
            return $text;
        }
    }
    
    
    public function getBloom()
    {
        return $this->bloom;
    }
    
    public function getText($paramsKey)
    {
        return $this->applyParams($this->text, $paramsKey);
    }
    
    public function getInput($paramsKey)
    {
        return $this->applyParams($this->input, $paramsKey);
    }
    
    public function getComments()
    {
        return $this->comments;
    }
    
    public function getPrefill($paramsKey)
    {
        return $this->applyParams($this->prefill, $paramsKey);
    }
    
    public function getHash()
    {
        return $this->hash;
    }
}

class InvalidQuestionDefinitionException extends \Exception
{
}
