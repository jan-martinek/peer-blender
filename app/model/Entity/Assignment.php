<?php

namespace Model\Entity;

use DateTime;

/**
 * @property int $id
 * @property Unit|NULL $unit m:hasOne
 * @property User|NULL $student m:hasOne(student_id)
 * @property DateTime $generated_at
 * @property string $preface
 * @property Question[] $questions m:belongsToMany
 * @property Answer[] $answers m:belongsToMany
 * @property string $rubrics
 * @property Solution|NULL $solution m:belongsToOne(assignment_id)
 */
class Assignment extends Entity
{
}
