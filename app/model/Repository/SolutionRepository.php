<?php

namespace Model\Repository;

use Model\Entity\Unit;
use Model\Entity\User;
use Exception;

class SolutionRepository extends Repository
{    
    public function findSolutionToReview(Unit $unit, User $reviewer, $userLimit = TRUE, $unitLimit = TRUE) 
    {
        $completeIds = $this->findAllComplete($unit);
        if (!count($completeIds)) {
            throw new SolutionToReviewNotFoundException();
            return FALSE;
        }
        
        $reviewedByMeIds = $this->findReviewedByMe($reviewer, $unit);
        if ($userLimit && count($reviewedByMeIds) >= $unit->course->reviewCount) {
            throw new ReviewLimitReachedException();
            return FALSE;
        }
        
        $groupedByReviewCount = $this->groupCompletedByReviewCount(
            $completeIds,
            $reviewedByMeIds,
            $reviewer->id  
        );
        
        if (count($groupedByReviewCount) === 0) {
            throw new SolutionToReviewNotFoundException();
            return FALSE;
        }
        
        $lowestReviewCount = min(array_keys($groupedByReviewCount));
        if ($unitLimit && $lowestReviewCount >= $unit->course->reviewCount) {
            throw new SolutionToReviewNotFoundException();
            return FALSE;
        }
        
        $randomId = array_rand($groupedByReviewCount[$lowestReviewCount]);
        
        return $this->find($randomId);
    }
    
    public function groupCompletedByReviewCount($completeIds, $reviewedByMeIds, $reviewerId) 
    {        
        return $this->connection->query(
            'SELECT solution.id, count(review.id) as reviewCount
              FROM solution 
              LEFT JOIN review ON review.solution_id = solution.id 
              WHERE solution.id IN %in', $completeIds,
              'AND solution.id NOT IN %in', $reviewedByMeIds,
              'AND solution.user_id != %i', $reviewerId,
            'GROUP BY solution.id')->fetchAssoc('reviewCount,id');     
    }
    
    public function findReviewedByMe(User $reviewer, Unit $unit = null) 
    {
        $ids = $this->connection->query(
            'SELECT solution.id AS id 
              FROM solution
              LEFT JOIN review ON solution.id = review.solution_id
              WHERE solution.unit_id = %i', $unit->id, 
              'AND reviewed_by_id = %i', $reviewer->id)
        ->fetchAssoc('id');
        
        return count($ids) ? array_keys($ids) : array(0);
    }
    
    public function findAllComplete(Unit $unit)
    {
        $attachmentOK = $this->connection->query(
            'SELECT id FROM solution WHERE unit_id = %i', $unit->id, 'AND attachment != ""'
        )->fetchAssoc('id');
        
        $incompleteIds = $this->connection->query(
            'SELECT solution.id
              FROM solution
              LEFT JOIN answer ON solution.id = answer.solution_id
              WHERE solution.unit_id = %i', $unit->id,
              'AND [answer].[text] = ""'
        )->fetchAssoc('id');
        
        return array_diff(array_keys($attachmentOK), array_keys($incompleteIds));
    }  
    
    public function findFavoriteByUnit(Unit $unit) 
    {   
        $solutions = $this->connection->query('SELECT solution.*, count(favorite.id) as favorites
            FROM solution 
            LEFT JOIN favorite ON (favorite.entity = "Solution" AND favorite.entity_id = solution.id) 
            WHERE unit_id = %i', $unit->id, '
            GROUP BY favorite.id
            HAVING favorites > 0
            ORDER BY favorites
            LIMIT 0, 5')->fetchAll();
        
        return count($solutions) ? $this->createEntities($solutions) : array();
    }
}

class SolutionToReviewNotFoundException extends Exception 
{    
}

class ReviewLimitReachedException extends Exception 
{    
}
