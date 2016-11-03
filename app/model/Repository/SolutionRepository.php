<?php

namespace Model\Repository;

use Model\Entity\Unit;
use Model\Entity\User;
use Exception;

class SolutionRepository extends Repository
{    
    public function findSolutionToReview(Unit $unit, User $reviewer, $userLimit = TRUE, $unitLimit = TRUE) 
    {
        $unitSolutionIds = $this->findIdsByUnit($unit);
        if (!count($unitSolutionIds)) {
            throw new SolutionToReviewNotFoundException();
            return FALSE;
        }
        
        $reviewedByMeIds = $this->findReviewedByMe($reviewer, $unit);
        if ($userLimit && count($reviewedByMeIds) >= $unit->course->reviewCount) {
            throw new ReviewLimitReachedException();
            return FALSE;
        }
        
        $groupedByReviewCount = $this->groupSolutionIdsByReviewCount(
            $unitSolutionIds,
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
    
    public function groupSolutionIdsByReviewCount($unitSolutionIds, $reviewedByMeIds, $reviewerId) 
    {        
        return $this->connection->query(
            'SELECT solution.id, count(review.id) as reviewCount
              FROM solution 
              LEFT JOIN review ON review.solution_id = solution.id 
              WHERE solution.id IN %in', $unitSolutionIds,
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
    
    public function findIdsByUnit(Unit $unit)
    {
        $allSolutionIds = $this->connection->query(
            'SELECT id FROM solution WHERE unit_id = %i', $unit->id)->fetchAssoc('id');
        
        return array_keys($allSolutionIds);
    }  
    
    
    /**
     * @param array
     */
    public function findFavoriteByUnits($units)
    {
        $unitIds = array();
        foreach ($units as $unit) {
            $unitIds[] = $unit->id;
        }
        
        $solutions = $this->connection->query('SELECT solution.id as solution_id, solution.*, unit_id, count(favorite.id) as favorites
            FROM solution 
            LEFT JOIN favorite ON (favorite.entity = "Solution" AND favorite.entity_id = solution.id) 
            WHERE unit_id IN %in', $unitIds, '
            GROUP BY favorite.id, solution.id
            HAVING favorites > 0
            ORDER BY favorites')->fetchAssoc('unit_id,solution_id');
        
        foreach ($solutions as $unitKey => $unit) {
            foreach ($unit as $solutionKey => $solution) {
                $solutions[$unitKey][$solutionKey] = $this->createEntity($solution);
            }
        }
        
        return $solutions;
    }
    
    public function findFavoriteByUnit(Unit $unit) 
    {   
        $solutions = $this->connection->query('SELECT solution.*, count(favorite.id) as favorites
            FROM solution 
            LEFT JOIN favorite ON (favorite.entity = "Solution" AND favorite.entity_id = solution.id) 
            WHERE unit_id = %i', $unit->id, '
            GROUP BY favorite.id, solution.id
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
