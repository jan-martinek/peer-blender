<?php

namespace Model\Ontology;

use Model\Entity\Question;

class QuestionDefinition extends \Nette\Object implements IDefinition
{
    /** @var CourseFactory */
    private $factory;
    
    /** @var QuestionRepository */
    private $repository;
    

    /** @var string Question's source filename */
    public $source;
    
    /** @var array Array of QuestionItems */
    private $questionItems = array();
    
    /** @var array Used up question items when question items may not be repeated */
    private $usedQuestionItemKeys = array();
    
    /** @var bool One question definition may be used multiple times */
    private $repeatAllowed = FALSE;
    
    /** @var array Array of Params objects */
    private $params = array();
    
    /** @var string hash of the data */
    private $hash;
    
    /** @var array Array of rubrics */
    private $rubrics = array();

    /** @var int Count of produced questions from this definition */
    private $assembledCount = 0;

    
    /** 
     * @param array
     * @param AssignmentDefinition
     */
    public function __construct($data, $factory)
    {
        $this->factory = $factory;
        $this->repository = $this->factory->questionRepository;
        $this->hash = substr(sha1(serialize($data)), 0, 6);
        $this->source = $data['filename'];
        
        if (isset($data['allowRepeat']) && $data['allowRepeat'] === 'question') {
            $this->repeatAllowed = TRUE;
        }
        
        $this->defineQuestionItems($data);
        
        if (isset($data['rubrics'])) {
            $builder = new RubricBuilder;
            $this->rubrics = $builder->buildSet($data['rubrics']);
        }
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
        
        // define params
        $params = $this->defineParams($data);
            
        // check and prepare questions
        if (!isset($data['questions'])) {
            throw new InvalidQuestionDefinitionException('No questions found.');
        } else if (is_string($data['questions'])) {
            $data['questions'] = array($data['questions']);
        }
        
        // define questions
        foreach ($data['questions'] as $item) {
            if (is_string($item)) {
                $this->questionItems[] = new QuestionItem($data, $item, $params);
            } else if (is_array($item)) {
                $this->defineQuestionItems($item, $data);
            } else {
                throw new InvalidQuestionDefinitionException();
            }
        }
    }
    
    
    /**
     * @param array
     * @return Params
     */
    private function defineParams($data)
    {
        // deprecated vars --> params
        if (isset($data['vars'])) {
            $data['params'] = $data['vars'];
            unset($data['vars']);
        }
        
        $paramsAvailable = (isset($data['params']) && is_array($data['params']));
        if ($paramsAvailable) {
            $params = new Params($data['params']);
            
            // allow repeat
            if (isset($data['allowRepeat']) && $data['allowRepeat'] === 'params') {
                $params->allowRepeat();
            }
            
            // register in unit
            if (in_array($params, $this->params)) {
                $params = $this->params[array_search($params, $this->params)];
            } else {
                $this->params[] = $params;
            }
        } else {
            $params = null;
        }
        return $params;
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
        $question->source = $this->source;
        $question->itemKey = $this->assembleQuestionItemKey();
        $question->paramsKey = $this->questionItems[$question->itemKey]->assembleParamsKey();
        $question->hash = $this->hash;
        $question->order = $this->assembledCount;
        
        /* Question texts are saved so that they're 
         * available in case the definition
         * changes and it's been already used somehow */
        $question->text = $this->questionItems[$question->itemKey]->getText($question->paramsKey);
        $question->prefill = $this->questionItems[$question->itemKey]->getPrefill($question->paramsKey);
        $question->input = $this->questionItems[$question->itemKey]->getInput($question->paramsKey);
        
        $this->repository->persist($question);
        
        $this->assembledCount++;
        return $question;
    }
    
    private function assembleQuestionItemKey()
    {
        $keys = array_keys($this->questionItems);
        $availableKeys = array_diff($keys, $this->usedQuestionItemKeys);
        if (count($availableKeys) === 0) {
            throw new Exception('Cannot generate more variations of the same question.');
            return;
        }
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
        $product->rubrics = $this->rubrics;
        $item = $this->getQuestionItem($question->itemKey);    
        
        $product->bloom = $item->bloom;
        $product->source = $this->source;
        $product->order = $question->order;
        $product->text = $item->getText($question->paramsKey);
        $product->prefill = $item->getPrefill($question->paramsKey);
        $product->input = $item->getInput($question->paramsKey);
        $product->comments = $item->comments;
        $product->hashMatch = $question->hash === $this->hash;
        
        if (!$product->hashMatch) {
            $product->textDump = ($question->text !== $product->text)
                ? $question->text 
                : null;
            $product->prefillDump = ($question->prefill !== $product->prefill)
                ? $question->prefill 
                : null;
            $product->inputDump = ($question->input !== $product->input)
                ? $question->input 
                : null;
        }
        
        return $product;
    }
    
    
    private function getQuestionItem($key)
    {
        if (isset($this->questionItems[$key])) {
            return $this->questionItems[$key];
        } else {
            return FALSE;
        }   
    }
}
