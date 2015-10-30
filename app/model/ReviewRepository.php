<?php

namespace Model\Repository;

use Model\Entity\Course;
use Model\Entity\Review;
use Model\Entity\Unit;
use Model\Entity\User;
use DateTime;

class ReviewRepository extends Repository
{
    public function createReview(Solution $solution, User $reviewer) 
    {
        $review = new Review;
        $review->solution = $solution;
        $review->reviewed_by = $reviewer;
        $review->opened_at = new DateTime;
        $this->persist($review);
        
        return $review;
    }    
    
    public function findUnfinishedReview(Unit $unit, User $reviewer) {
        $query = $this->connection->query(
            'SELECT review.* FROM review 
            LEFT JOIN solution ON solution_id = solution.id 
            WHERE solution.unit_id = %i', $unit->id,
            'AND review.reviewed_by_id = %i', $reviewer->id,
            'AND status = %s', Review::PREP);
        
        if ($openedReview = $query->fetch()) {
            return $this->createEntity($openedReview);
        } else {
            return false;
        }
    }
    
    public function findByUnitAndReviewer(Unit $unit, User $user, $onlyFinished = FALSE) {
        $where = array(
            'solution.unit_id%i' => $unit->id,
            'review.reviewed_by_id' => $user->id
        );
        
        if ($onlyFinished) {
            $where[] = array('review.status != %s', Review::PREP);
        }
        
        $query = $this->connection->query(
            'SELECT review.* FROM review 
            LEFT JOIN solution ON solution_id = solution.id 
            WHERE %and', $where,
            'ORDER BY opened_at');
        
        return $this->createEntities($query->fetchAll());
    }    
    
    public function findByUnit(Unit $unit) {
        $query = $this->connection->query(
            'SELECT review.* FROM review 
            LEFT JOIN solution ON solution_id = solution.id 
            WHERE solution.unit_id = %i', $unit->id,
            'ORDER BY opened_at');
        
        return $this->createEntities($query->fetchAll());
    }  
    
    public function findFavoriteByUnit(Unit $unit) 
    {   
        $reviewIds = array_keys($this->connection->query(
            'SELECT review.* FROM review 
            LEFT JOIN solution ON solution_id = solution.id 
            WHERE solution.unit_id = %i', $unit->id,
            'ORDER BY opened_at')->fetchAssoc('id'));
        
        $reviews = $this->connection->query('SELECT review.*, count(favorite.id) as favorites
            FROM review 
            LEFT JOIN favorite ON (favorite.entity = "Review" AND favorite.entity_id = review.id) 
            WHERE review.id IN %in', $reviewIds, '
            GROUP BY favorite.id
            HAVING favorites > 0
            ORDER BY favorites
            LIMIT 0, 5')->fetchAll();
        
        return $this->createEntities($reviews);
    }
    
    public function findReviewsWithProblemsByUserAndCourse(User $user, Course $course) {
        $query = $this->connection->query('SELECT review.* 
            FROM review 
            LEFT JOIN solution ON review.solution_id = solution.id
            LEFT JOIN unit ON solution.unit_id = unit.id
            WHERE review.status = %s', Review::PROBLEM,
            'AND review.reviewed_by_id = %i', $user->id,
            'AND course_id = %i', $course->id);
        
        return $this->createEntities($query->fetchAll());
    }
}
