<?php

namespace Model\Entity;

/**
 * @property int $id
 * @property string $text
 * @property Solution $solution m:hasOne
 * @property Question $question m:hasOne
 */
class Answer extends Entity
{
}
