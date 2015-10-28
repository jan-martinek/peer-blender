<?php

namespace Model\Entity;

/**
 * @property int $id
 * @property User $user m:hasOne
 * @property Course $course m:hasOne
 * @property string $role
 * @property bool $active
 */
class Enrollment extends Entity
{
}
