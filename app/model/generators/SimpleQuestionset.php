<?php

namespace Model\Generator;

use Nette;
use Model\Entity\Question;

class SimpleQuestionset extends Questionset implements IQuestionset
{
    
    public function __construct($objective, $questions = null) 
    {
        
        if (!is_null($questions)) {
            $this->setQuestions($questions);
        }
        $this->setBloom($objective);
    }
    
    
    /**
     * Sets question in markdown.
     * @return string
     */
    public function setQuestions(array $questions) 
    {
        $this->questions = $questions; 
    }
    
    /**
     * Adds a question by randomly combining provided parameters.
     */
    public function addRandomizedQuestion($questionTemplate, array $params, $count = 1) 
    {
        for ($i = 1; $i <= $count; $i++) {
            $question = $questionTemplate;
            foreach ($params as $name => $group) {
                $key = array_rand($group);
                $question = strtr($question, array('%' . $name . '%' => $group[$key]));
                unset($params[$name][$key]);
                unset($group[$key]);
            }
            $this->questions[] = $question;
        }
    }
    
    /**
     * Returns question in markdown.
     * @return string
     */
    public function getQuestions($count, $random = TRUE)
    {
        $questions = $this->questions;
        
        if ($random) {
            shuffle($questions);    
        } else {
            $this->questions = array_reverse($questions);
        }
        
        for ($i = 1; $i <= $count; $i++) {
            $question = new Question;
            $question->text = array_pop($questions);
            $question->type = 'plaintext';
            $assignedQuestions[] = $question;
        }

        return $assignedQuestions;
    }
    
}
