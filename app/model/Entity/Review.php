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
    
    public function calcScore() {
        $assessmentSet = json_decode($this->assessment);
        
        if (!is_object($assessmentSet)) {
            return FALSE;
        }
        
        $sum = 0;
        $count = 0;
        $solutionIsCompleteMultiplier = $this->solutionIsComplete ? 1 : 0.5;
        
        foreach ($assessmentSet as $assessment) {
            if (is_int($assessment)) {
                $sum += $assessment;
                $count++;
            }
        }
        
        if ($count > 0) {
            return round($sum / $count * $solutionIsCompleteMultiplier, 2);
        } else {
            return FALSE;
        }
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
