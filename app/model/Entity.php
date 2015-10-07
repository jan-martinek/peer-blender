<?php

namespace Model\Entity;

use DateTime;
use Model\Repository\FavoriteRepository;
use Model\Mailer;
use Nette\Security\Passwords;

class Entity extends \LeanMapper\Entity 
{
    public function getConventionalName() 
    {
        $name = array_slice(explode('\\', get_class($this)), -1, 1);
        return $name[0];
    }
}

class FavoritableEntity extends Entity 
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
        $favorite->entity = $this->getConventionalName();
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
            $user, $this->getConventionalName(), $this->id
        ) ? true : false;
    }
    
    public function getFavoriteByUser($user)
    {
        return $this->favoriteRepository->findByUserAndId(
            $user, $this->getConventionalName(), $this->id
        );
    }    
    
    public function countFavorites() 
    {
        return $this->favoriteRepository->countFavoritesOfEntity(
            $this->getConventionalName(), $this->id
        );
    }
    
    public function setFavoriteRepository(FavoriteRepository $repository) 
    {   
        $this->favoriteRepository = $repository;

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
class Assignment extends Entity
{
    /**
     * @return array
     */
    public function getQuestionSet() 
    {
        $set = @unserialize($this->questions);
        if ($set !== FALSE) {
            return $set;
        } else {
            return json_decode($this->questions, true);
        }
        
    }
    
    public function setQuestionSet($questions) 
    {
        $this->questions = json_encode((array) $questions);
    }
    
    /**
     * @return array
     */
    public function getRubricSet() 
    {
        $set = @unserialize($this->rubrics);
        if ($set !== FALSE) {
            return $set;
        } else {
            return json_decode($this->rubrics, true);
        }
        
    }
    
    public function setRubricSet($rubrics) 
    {
        $this->rubrics = json_encode((array) $rubrics);
    }
}

/**
 * @property int $id
 * @property string $name
 * @property string $goals
 * @property Unit[] $units m:belongsToMany
 * @property Enrollment[] $enrollments m:belongsToMany
 * @property int $reviewCount (review_count)
 * @property int $uploadMaxFilesizeKb (upload_max_filesize_kb)
 * @property string $gaCode (ga_code)
 */
class Course extends Entity
{
}


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
class Objection extends Entity
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
    /**
     * @return array
     */
    public function getAssessmentSet() 
    {
        $set = @unserialize($this->assessment);
        if ($set !== FALSE) {
            return $set;
        } else {
            return json_decode($this->assessment, TRUE);
        }
        
    }
    
    public function setAssessmentSet($assessment) 
    {
        $this->assessment = json_encode((array) $assessment);
    }
}

/**
 * @property int $id
 * @property Unit $unit m:hasOne
 * @property Assignment $assignment m:hasOne
 * @property User $user m:hasOne
 * @property DateTime $submitted_at
 * @property DateTime $edited_at
 * @property string $answer
 * @property string|NULL $attachment
 * @property Review[]|NULL $reviews m:belongsToMany(solution_id)
 */
class Solution extends Entity
{   
    public function getScore() 
    {
        if (is_null($this->reviews)) {
            return FALSE;
        }
        
        $scores = array();
        
        foreach ($this->reviews as $review) {
            if (!is_null($review->score)) {
                $scores[] = $review->score;    
            }
        }
        
        return count($scores) ? array_sum($scores) / count($scores) : FALSE;
    }
    
    /**
     * @return array
     */
    public function getAnswerSet() 
    {
        $set = @unserialize($this->answer);
        if ($set !== FALSE) {
            return $set;
        } else {
            return json_decode($this->answer, true);
        }
        
    }
    
    public function setAnswerSet($answer) 
    {
        $this->answer = json_encode((array) $answer);
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
 * @property Assignment[] $assignments m:belongsToMany
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
    
    public function getPhaseNames() {
        return array( 
            0 => 'draft', 
            1 => 'published', 
            2 => 'reviews', 
            3 => 'objections', 
            4 => 'finalized'
        );
    }
}

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|NULL $passwordResetToken (password_reset_token)
 * @property DateTime|NULL $passwordResetValidUntil (password_reset_valid_until)
 * @property Enrollment[] $enrollments m:belongsToMany
 * @property Assignment[] $assignments m:belongsToMany
 * @property Review[] $reviews m:belongsToMany(reviewed_by_id:review)
 * @property Solution[] $solutions m:belongsToMany
 */
class User extends FavoritableEntity
{
    private $passwordResetTimespan = '+ 30 minute';
    
    public function initiatePasswordReset() {
        $this->passwordResetToken = substr(md5(rand()), 0, 10);
        $this->passwordResetValidUntil = new DateTime($this->passwordResetTimespan);
    }
    
    public function hasPasswordResetBeenInitiated($token) {
        return ($this->passwordResetValidUntil >= new DateTime 
            && $this->passwordResetToken === $token) 
            ? true : false;
    }
    
    public function sendPasswordResetEmail($presenter) {
        $mailer = new Mailer;
        $mailer->sendPasswordResetEmail($presenter, $this->email, $this->passwordResetToken);
    }
}

/* DATA GATHERING */

/**
 * @property int $id
 * @property User $user m:hasOne
 * @property string $entity_name
 * @property int $entity_identifier
 * @property string $action
 * @property DateTime $logged_at
 */
class Log extends Entity
{   
}
