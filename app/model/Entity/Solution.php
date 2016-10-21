<?php

namespace Model\Entity;

use DateTime;

/**
 * @property int $id
 * @property Unit $unit m:hasOne
 * @property Assignment $assignment m:hasOne
 * @property User $user m:hasOne
 * @property DateTime $submitted_at
 * @property DateTime $edited_at
 * @property Answer[]|NULL $answers m:belongsToMany
 * @property string|NULL $attachment
 * @property Review[]|NULL $reviews m:belongsToMany(solution_id)
 */
class Solution extends FavoritableEntity
{   
    public function getScore() 
    {
        if (is_null($this->reviews)) {
            return FALSE;
        }
        
        $scores = array();
        
        foreach ($this->reviews as $review) {
            if ($review->isOk()) {
                $scores[] = $review->score;    
            }
        }
        
        return count($scores) ? array_sum($scores) / count($scores) : FALSE;
    }

    /**
     * Checks whether all questions have been answered and a file has been submitted
     */
    public function isComplete()
    {
        foreach ($this->answers as $answer) {
            if (!$answer->isComplete()) {
                return FALSE;
            }
        }
        return TRUE;
    }
}
