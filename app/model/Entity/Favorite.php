<?php

namespace Model\Entity;

use DateTime;

/**
 * @property int $id
 * @property User $user m:hasOne
 * @property string $entity
 * @property int $entity_id
 * @property DateTime $saved_at
 */
class Favorite extends Entity
{
}
