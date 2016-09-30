<?php

namespace Model\Entity;

/**
 * @property int $id
 * @property User $user m:hasOne
 * @property Course $course m:hasOne
 * @property string $role
 * @property bool $active
 * @property string $notes
 * @property string $hiddenNotes (hidden_notes)
 * @property float $scoreAdjustment (score_adjustment)
 */
class Enrollment extends Entity
{
}
