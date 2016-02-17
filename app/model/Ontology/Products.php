<?php

namespace Model\Ontology;

use DateTime;
use Nette\Object;
use Model\Entity\Entity;

abstract class AbstractProduct extends Object
{
	public $id;
	public $entity;
	
	public function __construct(Entity $entity)
	{
		$this->entity = $entity;
		$this->id = !$entity->isDetached() ? $entity->id : null;
	}
}

class CourseProduct extends AbstractProduct
{
	public $author;
	public $name;
	public $goals;
	public $methods;
	public $support;
	public $reviewCount;
	public $uploadMaxFilesizeKb;
	public $gaCode;
}

class UnitProduct extends AbstractProduct
{    
    public $name;
    public $goals;
    public $reading;
    public $preface;
    public $rubrics;
    
    public $published_since;
    public $reviews_since;
    public $objections_since;
    public $finalized_since;
    
    const
        DRAFT = 0,
        PUBLISHED = 1,
        REVIEWS = 2,
        OBJECTIONS = 3,
        FINALIZED = 4;
    
    public function getCurrentPhase() 
    {
        if ($this->finalized_since < new DateTime) {
            return self::FINALIZED;
        } else if ($this->objections_since < new DateTime) {
            return self::OBJECTIONS;
        } else if ($this->reviews_since < new DateTime) {
            return self::REVIEWS;
        } else if ($this->published_since < new DateTime) {
            return self::PUBLISHED;
        } else {
            return self::DRAFT;
        }
    }
    
    public function isCurrentPhase($phase)
    {
        return $phase === $this->getCurrentPhase() ? true : false;
    }
    
    public function getCurrentPhaseName() 
    {
        $phase = $this->getCurrentPhase();
        switch ($phase) {
            case self::FINALIZED:
                return 'finalized';
            case self::OBJECTIONS:
                return 'objections';
            case self::REVIEWS:
                return 'reviews';
            case self::PUBLISHED:
                return 'published';
            case self::DRAFT:
                return 'draft';
        }
    }
    
    public function getPhaseNames() 
    {
        return array( 
            0 => 'draft', 
            1 => 'published', 
            2 => 'reviews', 
            3 => 'objections', 
            4 => 'finalized'
        );
    }
    
    public function getNextPhaseName() 
    {
        $phase = $this->getCurrentPhase();
        switch ($phase) {
            case self::FINALIZED:
                return FALSE;
            case self::OBJECTIONS:
                return 'finalized';
            case self::REVIEWS:
                return 'objections';
            case self::PUBLISHED:
                return 'reviews';
            case self::DRAFT:
                return 'published';
        }
    }
    
    public function hasBeenPublished() 
    {
        return in_array($this->getCurrentPhase(), array(self::PUBLISHED, self::REVIEWS, self::OBJECTIONS, self::FINALIZED));
    }
    
    public function hasReviewsPhaseStarted() 
    {
        return in_array($this->getCurrentPhase(), array(self::REVIEWS, self::OBJECTIONS, self::FINALIZED));
    }
    
    public function hasObjectionsPhaseStarted() 
    {
        return in_array($this->getCurrentPhase(), array(self::OBJECTIONS, self::FINALIZED));
    }
    
    public function isFinalized() 
    {
        return in_array($this->getCurrentPhase(), array(self::FINALIZED));
    }
}

class AssignmentProduct extends AbstractProduct
{
	public $generated_at;
	public $questions = array();
}

class QuestionProduct extends AbstractProduct
{
	public $bloom;
	public $input;
	public $text;
	public $prefill;
	public $comments;
	
	public $order;
	
	public $hashMatch;
	public $textDump;
	public $prefillDump;
	
	/**
	 * Checks whether syntax highlighting is 
	 * available for question's input method.
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
}
