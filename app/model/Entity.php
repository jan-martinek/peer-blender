<?php

namespace Model\Entity;

use DateTime;
use Model\Repository\FavoriteRepository;

class FavoritableEntity extends \LeanMapper\Entity 
{
    private $favoriteRepository;
    
    public function favorite($user) 
    {
        if ($favorite = $this->getFavoriteByUser($user))
        {
            $this->favoriteRepository->delete($favorite->id);
            return true;
        }

        $favorite = new \Model\Entity\Favorite;
        $favorite->user = $user;
        $favorite->entity = $this->getConventionalEntityName();
        $favorite->entity_id = $this->id;
        $favorite->saved_at = new DateTime;
        $this->favoriteRepository->persist($favorite);
        return true;
    }
    
    public function removeFavorite($user) 
    {
        
    }
    
    public function isFavoritedBy($user) 
    {
        return $this->favoriteRepository->findByUserAndId(
            $user, $this->getConventionalEntityName(), $this->id
        ) ? true : false;
    }
    
    public function getFavoriteByUser($user)
    {
        return $this->favoriteRepository->findByUserAndId(
            $user, $this->getConventionalEntityName(), $this->id
        );
    }    
    
    public function countFavorites() 
    {
        return $this->favoriteRepository->countFavoritesOfEntity(
            $this->getConventionalEntityName(), $this->id
        );
    }
    
    public function setFavoriteRepository(FavoriteRepository $repository) 
    {   
        $this->favoriteRepository = $repository;

    }
    
    public function getConventionalEntityName() 
    {
        $name = array_slice(explode('\\', get_class($this)), -1, 1);
        return $name[0];
    }
}

/**
 * @property int $id
 * @property Unit $unit m:hasOne
 * @property User $student m:hasOne(student_id)
 * @property DateTime $generated_at
 * @property string $preface
 * @property string $questions
 * @property string $rubrics
 * @property Solution|NULL $solution m:belongsToOne(assignment_id)
 */
class Assignment extends \LeanMapper\Entity
{
}

/**
 * @property int $id
 * @property string $name
 * @property string $goals
 * @property Unit[] $units m:belongsToMany
 * @property Enrollment[] $enrollments m:belongsToMany
 * @property int $reviewCount
 */
class Course extends \LeanMapper\Entity
{
}


/**
 * @property int $id
 * @property User $user m:hasOne
 * @property Course $course m:hasOne
 * @property string $role
 * @property bool $active
 */
class Enrollment extends \LeanMapper\Entity
{
}

/**
 * @property int $id
 * @property User $user m:hasOne
 * @property string $entity
 * @property int $entity_id
 * @property DateTime $saved_at
 */
class Favorite extends \LeanMapper\Entity
{
}

/**
 * @property int $id
 * @property Review $review m:hasOne
 * @property User $user m:hasOne
 * @property DateTime $submitted_at
 * @property string $objection
 * @property bool $evaluated
 * @property User|NULL $arbiter m:hasOne
 * @property bool $legitimate
 * @property string $comment
 * @property DateTime $evaluated_at
 */
class Objection extends \LeanMapper\Entity
{
}

/**
 * @property int $id
 * @property Solution $solution m:hasOne
 * @property User $reviewed_by m:hasOne(reviewed_by_id)
 * @property DateTime $opened_at
 * @property int|NULL $score
 * @property string|NULL $assessment
 * @property string|NULL $comments
 * @property DateTime|NULL $submitted_at
 * @property Objection|NULL $objection
 */
class Review extends FavoritableEntity
{
}

/**
 * @property int $id
 * @property Unit $unit m:hasOne
 * @property Assignment $assignment m:hasOne
 * @property User $user m:hasOne
 * @property DateTime $submitted_at
 * @property DateTime $edited_at
 * @property string $answer
 * @property string $attachment
 * @property Review[]|NULL $reviews m:belongsToMany(solution_id)
 */
class Solution extends \LeanMapper\Entity
{
    public function getScore() 
    {
        if (is_null($this->reviews)) {
            return false;
        }
        
        $scores = array();
        
        foreach ($this->reviews as $review) {
            if (!is_null($review->score)) {
                $scores[] = $review->score;    
            }
        }        
    }
}

/**
 * @property int $id
 * @property Course $course m:hasOne
 * @property DateTime $published_since
 * @property DateTime $reviews_since
 * @property DateTime $objections_since
 * @property DateTime $finalized_since
 * @property string $name
 * @property string $goals
 * @property string $reading
 * @property string $generator
 * @property Assignment[] $assignments m:hasMany
 */
class Unit extends FavoritableEntity
{
    const
        DRAFT = 0,
        PUBLISHED = 1,
        REVIEWS = 2,
        OBJECTIONS = 3,
        FINALIZED = 4;
    
    public function getCurrentPhase() {
        if ($this->finalized_since < new DateTime) {
            return self::FINALIZED;
        } else if ($this->objections_since < new DateTime) {
            return self::OBJECTIONS;
        } else if ($this->reviews_since < new DateTime) {
            return self::REVIEWS;
        } else if ($this->published_since < new DateTime) {
            return self::PUBLISHED;
        } else {
            return self::DRAFT;
        }
    }
    
    public function isCurrentPhase($phase) {
        return $phase === $this->getCurrentPhase() ? true : false;
    }
    
    public function getCurrentPhaseName() {
        $phase = $this->getCurrentPhase();
        switch ($phase) {
            case self::FINALIZED:
                return 'finalized';
            case self::OBJECTIONS:
                return 'objections';
            case self::REVIEWS:
                return 'reviews';
            case self::PUBLISHED:
                return 'published';
            case self::DRAFT:
                return 'draft';
        }
    }
}

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property Enrollment[] $enrollments m:belongsToMany
 * @property Assignment[] $assignments m:belongsToMany
 * @property Review[] $reviews m:belongsToMany(reviewed_by_id:review)
 * @property Solution[] $solutions m:belongsToMany
 */
class User extends FavoritableEntity
{
}
