<?php

namespace Model\Entity;

use DateTime;

/**
 * @property int $id
 * @property Unit $unit m:hasOne
 * @property User $student m:hasOne(student_id)
 * @property DateTime $generated_at
 * @property string $preface
 * @property Question[] $questions m:belongsToMany
 * @property Answer[] $answers m:belongsToMany
 * @property string $rubrics
 * @property Solution|NULL $solution m:belongsToOne(assignment_id)
 */
class Assignment extends Entity
{   
    /**
     * @return array
     */
    public function getRubricSet() 
    {
        $set = @unserialize($this->rubrics);
        if ($set !== FALSE) {
            return $set;
        } else {
            return json_decode($this->rubrics, true);
        }
        
    }
    
    public function setRubricSet($rubrics) 
    {
        $this->rubrics = json_encode((array) $rubrics);
    }
}
