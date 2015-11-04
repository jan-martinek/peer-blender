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
}
