<?php

namespace Model\Repository;

use Model\Entity\Review;
use DateTime;

class ReviewRepository extends Repository
{
    public function createReview($solution, $reviewer) 
    {
        $review = new Review;
        $review->solution = $solution;
        $review->reviewed_by = $reviewer;
        $review->opened_at = new DateTime;
        $this->persist($review);
        
        return $review;
    }    
    
    public function findUnfinishedReview($unit, $reviewer) {
        $query = $this->connection->query(
            'SELECT review.* FROM review 
            LEFT JOIN solution ON solution_id = solution.id 
            WHERE solution.unit_id = %i', $unit->id,
            'AND review.reviewed_by_id = %i', $reviewer->id,
            'AND (score IS NULL OR comments IS NULL OR review.submitted_at IS NULL)');
        
        if ($openedReview = $query->fetch()) {
            return $this->createEntity($openedReview);
        } else {
            return false;
        }
    }
    
    public function findByUnitAndReviewer($unit, $user, $onlyFinished = FALSE) {
        $where = array(
            'solution.unit_id%i' => $unit->id,
            'review.reviewed_by_id' => $user->id
        );
        
        if ($onlyFinished) {
            $where[] = array('review.score IS NOT %sN', '');
        }
        
        $query = $this->connection->query(
            'SELECT review.* FROM review 
            LEFT JOIN solution ON solution_id = solution.id 
            WHERE %and', $where,
            'ORDER BY opened_at');
        
        return $this->createEntities($query->fetchAll());
    }    
    
    public function findByUnit($unit) {
        $query = $this->connection->query(
            'SELECT review.* FROM review 
            LEFT JOIN solution ON solution_id = solution.id 
            WHERE solution.unit_id = %i', $unit->id,
            'ORDER BY opened_at');
        
        return $this->createEntities($query->fetchAll());
    }  
    
    public function findFavoriteByUnit($unit) 
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
}
