<?php

namespace Model\Entity;

/**
 * @property int $id
 * @property string $name
 * @property string $goals
 * @property string $methods
 * @property string $support
 * @property string $footer
 * @property Unit[] $units m:belongsToMany
 * @property Enrollment[] $enrollments m:belongsToMany
 * @property int $reviewCount (review_count)
 * @property int $uploadMaxFilesizeKb (upload_max_filesize_kb)
 * @property string|NULL $gaCode (ga_code)
 */
class Course extends Entity
{
}
