<?php

namespace Model\Entity;

/**
 * @property int $id
 * @property string|NULL $text
 * @property string|NULL $comments
 * @property Solution $solution m:hasOne
 * @property Question $question m:hasOne
 */
class Answer extends Entity
{
    /**
     * Checks whether the question has been answered
     */
    public function isComplete()
    {
        if (trim($this->text) === '' || $this->text === $this->question->prefill) {
            return FALSE;
        }
        
        return TRUE;
    }
}
