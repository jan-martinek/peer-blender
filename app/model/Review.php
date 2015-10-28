<?php

namespace Model\Entity;

use DateTime;

/**
 * @property int $id
 * @property Solution $solution m:hasOne
 * @property User $reviewed_by m:hasOne(reviewed_by_id)
 * @property DateTime $opened_at
 * @property int|NULL $score
 * @property string|NULL $assessment
 * @property string|NULL $comments
 * @property DateTime|NULL $submitted_at
 * @property Objection|NULL $objection m:belongsToOne
 */
class Review extends FavoritableEntity
{
    
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
}
