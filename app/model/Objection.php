<?php

namespace Model\Entity;

use DateTime;

/**
 * @property int $id
 * @property Review $review m:hasOne
 * @property User $user m:hasOne(user_id)
 * @property DateTime $submitted_at
 * @property string $objection
 * @property bool $evaluated
 * @property User|NULL $arbiter m:hasOne(arbiter_id)
 * @property bool $legitimate
 * @property string $comment
 * @property DateTime $evaluated_at
 */
class Objection extends Entity
{
}
