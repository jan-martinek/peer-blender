<?php

namespace Model\Entity;

/**
 * @property int $id
 * @property string $dir
 * @property bool $open
 * @property string $name
 * @property Unit[] $units m:belongsToMany
 * @property Enrollment[] $enrollments m:belongsToMany
 * @property int $reviewCount (review_count)
 * @property int $uploadMaxFilesizeKb (upload_max_filesize_kb)
 * @property string|NULL $gaCode (ga_code)
 */
class Course extends Entity
{
}
