<?php

namespace Model\Generator;

use Nette;

abstract class Questionset extends Nette\Object implements IQuestionset
{
    protected $questions;
    protected $bloom;
    protected $type = 'plaintext';
    public $prefill = '';
    
    /**
     * Sets the classification of the question in Revised 
     * Bloom's Taxonomy of Education Objectives
     * (Anderson, L., & Krathwohl, D. A. (2001). Taxonomy for Learning, 
     * Teaching and Assessing: A Revision of Bloom's Taxonomy of 
     * Educational Objectives. New York: Longman.)
     * @return bool
     */
    public final function setBloom($objective) 
    {
        $objectives = array(
            'remember',
            'understand',
            'apply',
            'analyze',
            'evaluate',
            'create'
        );
        
        if (in_array($objective, $objectives)) {
            $this->bloom = $objective;
        } else {
            throw new Exception('This objective does not belong to the revised Bloom\'s taxonomy.');
        }
    }
    
    /**
     * Returns the classification of the question in Revised 
     * Bloom's Taxonomy of Education Objectives
     * @return string
     */
    public final function getBloom() 
    {
        return $this->bloom;
    }
    
    public function setType($type)
    {
        $types = array(
            'plaintext',
            'code',
            'markdown',
            'javascript',
            'html',
            'sql',
            'css',
            'xml'
        );
        
        if (in_array($type, $types)) {
            $this->type = $type;
        } else {
            throw new Exception('This type is not supported.');
        }
    }
    
    public final function getType() 
    {
        return $this->type;
    }
}
