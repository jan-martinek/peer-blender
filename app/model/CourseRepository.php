<?php

namespace Model\Repository;

use Model\Entity\Course;
use Model\Entity\Review;
use Exception;
use DateTime;

class CourseRepository extends Repository
{   
    public function getReviewStats(Course $course) 
    {
        $units = array();
        foreach ($course->units as $unit) {
            $units[] = $unit->id;
        }
        return $this->connection->query('SELECT solution.unit_id, count(*), AVG(score), std(score) FROM review
            JOIN solution ON solution.id = review.solution_id
            WHERE unit_id IN %in', $units,
            'AND review.status = %s', Review::OK,
            'GROUP BY unit_id')->fetchAll();
    }
    
    public function getSubmittedReviewsStats(Course $course) 
    {
        return $this->connection->query('SELECT unit.id as unit_id, reviewed_by_id, count(review.id) as reviewCount 
            FROM review 
            JOIN solution ON review.solution_id = solution.id
            JOIN unit ON solution.unit_id = unit.id
            WHERE course_id = %i', $course->id,
            'AND review.status = %s', Review::OK,
            'GROUP BY reviewed_by_id,unit_id'
        )->fetchAssoc('reviewed_by_id,unit_id');
    }
    
    public function getProblemReviewsStats(Course $course)
    {
        $own = $this->connection->query('SELECT reviewed_by_id, count(review.id) as reviewCount, GROUP_CONCAT(review.status) as statuses
            FROM review 
            JOIN solution ON review.solution_id = solution.id
            JOIN unit ON solution.unit_id = unit.id
            WHERE course_id = %i', $course->id,
            'AND review.status != %s', Review::OK,
            'AND review.status != %s', Review::PREP,
            'GROUP BY reviewed_by_id'
        )->fetchAssoc('reviewed_by_id');
        
        $fromOthers = $this->connection->query('SELECT solution.user_id as reviewed_user_id, count(review.id) as reviewCount,  GROUP_CONCAT(review.status) as statuses 
            FROM review 
            JOIN solution ON review.solution_id = solution.id
            JOIN unit ON solution.unit_id = unit.id
            WHERE course_id = %i', $course->id,
            'AND review.status != %s', Review::OK,
            'AND review.status != %s', Review::PREP,
            'GROUP BY reviewed_user_id'
        )->fetchAssoc('reviewed_user_id');

        $problems = array();
        foreach ($own as $userId => $desc) {
            $problems[$userId]['own'] = $desc;
        }
        foreach ($fromOthers as $userId => $desc) {
            $problems[$userId]['fromOthers'] = $desc;
        }
        
        return $problems;
    }
}
