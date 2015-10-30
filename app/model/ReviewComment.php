<?php

namespace Model\Entity;

use DateTime;

/**
 * @property int $id
 * @property Review $review m:hasOne
 * @property DateTime $submitted_at
 * @property User $author m:hasOne(user_id)
 * @property string $comment
 * @property string $review_status
 */
class ReviewComment extends FavoritableEntity
{
}
