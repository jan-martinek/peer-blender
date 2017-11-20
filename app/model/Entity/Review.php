<?php

namespace Model\Entity;

use DateTime;

/**
 * @property int $id
 * @property Solution $solution m:hasOne
 * @property User $reviewed_by m:hasOne(reviewed_by_id)
 * @property DateTime $opened_at
 * @property float|NULL $score
 * @property string|NULL $assessment
 * @property bool $solutionIsComplete (solution_is_complete)
 * @property string|NULL $notes
 * @property DateTime|NULL $submitted_at
 * @property bool $submittedInTime (submitted_in_time)
 * @property string $status
 * @property ReviewComment[]|NULL $comments m:belongsToMany
 */
class Review extends FavoritableEntity
{
    const
        PREP = 'prep',
        OK = 'ok',
        PROBLEM = 'problem',
        OBJECTION = 'objection',
        FIXED = 'fixed';
    
    /**
     * @return array
     */
    public function getAssessmentSet() 
    {
        $set = @unserialize($this->assessment);
        if ($set !== FALSE) {
            return $set;
        } else {
            return json_decode($this->assessment, TRUE);
        }
        
    }
    
    public function setAssessmentSet($assessment) 
    {
        $this->assessment = json_encode((array) $assessment);
    }

    public function isInPrep() 
    {
        return $this->status == self::PREP ? true : false;
    }
    
    public function isOk() 
    {
        return $this->status == self::OK ? true : false;
    }
    
    public function hasProblem() 
    {
        return $this->status == self::PROBLEM ? true : false;
    }
    
    public function isObjected() 
    {
        return $this->status == self::OBJECTION ? true : false;
    }
    
    public function isFixed() 
    {
        return $this->status == self::FIXED ? true : false;
    }
}
