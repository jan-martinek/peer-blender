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
    
    public function getCurrentPhase()
    {
        return $this->entity->getCurrentPhase();
    }
    
    public function isCurrentPhase($phase)
    {
        return $this->entity->isCurrentPhase($phase);
    }
    
    public function getCurrentPhaseName() 
    {
        return $this->entity->getCurrentPhaseName();
    }
    
    public function getPhaseNames() 
    {
        return $this->entity->getPhaseNames();
    }
    
    public function getNextPhaseName() 
    {
        return $this->entity->getNextPhaseName();
    }
    
    public function hasBeenPublished() 
    {
        return $this->entity->hasBeenPublished();
    }
    
    public function hasReviewsPhaseStarted() 
    {
        return $this->entity->hasReviewsPhaseStarted();
    }
    
    public function hasObjectionsPhaseStarted() 
    {
        return $this->entity->hasObjectionsPhaseStarted();
    }
    
    public function isFinalized() 
    {
        return $this->entity->isFinalized();
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
