<?php

namespace Model\Entity;

use DateTime;

/**
 * @property int $id
 * @property string $name
 * @property Course $course m:hasOne
 * @property DateTime $published_since
 * @property DateTime $reviews_since
 * @property DateTime $objections_since
 * @property DateTime $finalized_since
 * @property string $def
 * @property Assignment[] $assignments m:belongsToMany
 */
class Unit extends FavoritableEntity
{
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
