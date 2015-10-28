<?php

namespace Model\Repository;

use Exception;
use DateTime;

class CourseRepository extends Repository
{   
    public function getReviewStats(\Model\Entity\Course $course) {
        $units = array();
        foreach ($course->units as $unit) {
            $units[] = $unit->id;
        }
        return $this->connection->query('SELECT solution.unit_id, count(*), AVG(score), std(score) FROM review
            JOIN solution ON solution.id = review.solution_id
            WHERE unit_id IN %in', $units,
            'GROUP BY unit_id')->fetchAll();
    }
}
