<?php

namespace Model\Entity;

/**
 * @property int $id
 * @property int|NULL $order
 * @property int $itemKey (item_key)
 * @property int|NULL $varsKey (vars_key)
 * @property string $hash
 * @property string $text
 * @property string $prefill
 * @property Assignment $assignment m:hasOne 
 * @property Answer|NULL $answer m:belongsToOne
 */
class Question extends Entity
{   
}
