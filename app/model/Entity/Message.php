<?php

namespace Model\Entity;

use DateTime;

/**
 * @property int $id
 * @property User $user m:hasOne
 * @property Course $course m:hasOne
 * @property Unit|NULL $unit m:hasOne
 * @property DateTime $submitted_at
 * @property string $text 
 */
class Message extends FavoritableEntity
{
}
