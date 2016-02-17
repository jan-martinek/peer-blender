<?php

namespace Model\Ontology;

use Model\Entity\Question;

class QuestionDefinition extends \Nette\Object implements IDefinition
{
    /** @var CourseFactory */
    private $factory;
    
    /** @var QuestionRepository */
    private $repository;
    
    
    /** @var array Array of QuestionItems */
    private $questionItems = array();
    
    /** @var array Used up question items when question items may not be repeated */
    private $usedQuestionItemKeys = array();
    
    /** @var bool One question definition may be used multiple times */
    private $repeatAllowed = FALSE;
    
    /** @var array Array of Vars objects */
    private $vars = array();
    
    /** @var string hash of the data */
    private $hash;
    
    /** 
     * @param array
     * @param AssignmentDefinition
     */
    public function __construct($data, $factory) 
    {
        $this->factory = $factory;
        $this->repository = $this->factory->questionRepository;
        $this->hash = substr(sha1(serialize($data)), 0, 6);
        
        if (isset($data['allowRepeat']) && $data['allowRepeat'] === 'question') {
            $this->repeatAllowed = TRUE;
        }
        
        $this->defineQuestionItems($data);
    }
    
    /**
     * Defines questions and adds them to the assignment structure.
     * @param QuestionDefinition
     * @param array question data
     * @param array parent question data used in param inheritance
     * @return QuestionDefinition
     */
    private function defineQuestionItems($data, $inherit = array()) 
    {
        // inherit from parent
        if (count($inherit)) {
            $data = array_merge($inherit, $data);
        }
        
        // define vars
        $vars = $this->defineVars($data);
            
        // check and prepare questions
        if (!isset($data['questions'])) {
            throw new InvalidQuestionDefinitionException('No questions found.');
        } else if (is_string($data['questions'])) {
            $data['questions'] = array($data['questions']);
        }
        
        // define questions
        foreach ($data['questions'] as $item) {
            if (is_string($item)) {
                $this->questionItems[] = new QuestionItem($data, $item, $vars);
            } else if (is_array($item)) {
                $this->defineQuestionItems($item, $data);
            } else {
                throw new InvalidQuestionDefinitionException();
            }
        }
    }
    
    
    /**
     * @param array
     * @return Vars
     */
    private function defineVars($data)
    {
        $varsAvailable = (isset($data['vars']) && is_array($data['vars']));
        if ($varsAvailable) {
            $vars = new Vars($data['vars']);
            
            // allow repeat
            if (isset($data['allowRepeat']) && $data['allowRepeat'] === 'vars') {
                $vars->allowRepeat();
            }
            
            // register in unit
            if (in_array($vars, $this->vars)) {
                $vars = $this->vars[array_search($vars, $this->vars)];
            } else {
                $this->vars[] = $vars;
            }
        } else {
            $vars = null;
        }
        return $vars;
    }
    
    
    /**
     * Get all questions' keys
     * @return array
     */
    public function getKeys() 
    {
        return array_keys($this->questionDefinitions);
    }
    
    /**
     * Get questions' count
     * @return int
     */
    public function count() 
    {
        return count($this->questionDefinitions);
    }
    
    
    /**
     * Assembles a new question.
     * @return Model\Entity\Question
     */
    public function assemble()
    {
        $question = new Question();
        $question->itemKey = $this->assembleQuestionItemKey();
        $question->varsKey = $this->questionItems[$question->itemKey]->assembleVarsKey();
        $question->hash = $this->hash;
        
        /* Question texts are saved so that they're 
         * available in case the definition
         * changes and it's been already used somehow */
        $question->text = $this->questionItems[$question->itemKey]->getText($question->varsKey);
        $question->prefill = $this->questionItems[$question->itemKey]->getPrefill($question->varsKey);
        
        $this->repository->persist($question);
        
        return $question;
    }
    
    private function assembleQuestionItemKey()
    {
        $keys = array_keys($this->questionItems);
        $availableKeys = array_diff($keys, $this->usedQuestionItemKeys);
        $key = $availableKeys[array_rand($availableKeys)];
        
        if (!$this->repeatAllowed) {
            $this->usedQuestionItemKeys[] = $key;
        }
        
        return $key;
    }
    
    
    /**
     * Produces a question from saved entity.
     * @param Model\Entity\Question
     * @return Model\Ontology\QuestionProduct
     */
    public function produce($question)
    {
        $product = new QuestionProduct($question);
        
        $item = $this->getQuestionItem($question->itemKey);
        
        $product->bloom = $item->bloom;
        $product->text = $item->getText($question->varsKey);
        $product->prefill = $item->getPrefill($question->varsKey);
        $product->input = $item->getInput($question->varsKey);
        $product->comments = $item->comments;
        
        $product->hashMatch = $question->hash === $item->hash ? true : false;
        
        if (!$product->hashMatch) {
            $product->textDump = ($question->text !== $product->text)
                ? $question->text : null;
            $product->prefillDump = ($question->prefill !== $product->prefill)
                ? $question->prefill : null;
        }
        
        return $product;
    }
    
    
    private function getQuestionItem($key)
    {
        if (isset($this->questionItems[$key])) {
            return $this->questionItems[$key];
        } else {
            throw new InvalidQuestionDefinitionException('Selected questionItem does not exist.');    
        }
        
    }
    
    
    private function produceQuestionItemKey()
    {
        
        
        return $key;
    }
}
