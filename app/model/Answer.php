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
}
