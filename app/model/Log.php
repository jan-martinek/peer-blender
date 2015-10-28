<?php

namespace Model\Entity;

use DateTime;

/**
 * @property int $id
 * @property User $user m:hasOne
 * @property string $entity_name
 * @property int $entity_identifier
 * @property string $action
 * @property DateTime $logged_at
 */
class Log extends Entity
{   
}
