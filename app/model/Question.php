<?php

namespace Model\Entity;

/**
 * @property int $id
 * @property int $order
 * @property string $type
 * @property string $text
 * @property string $prefill
 * @property Assignment $assignment m:hasOne 
 * @property Answer|NULL $answer m:belongsToOne
 */
class Question extends Entity
{
}
