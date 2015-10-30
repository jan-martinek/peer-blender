<?php

namespace Model\Repository;

use Model\Entity\Course;
use Model\Entity\User;

class EnrollmentRepository extends Repository
{
    public function getRoleInCourse(User $user, Course $course) {
        $params = array(
            'user_id%i' => $user->id,
            'course_id%i' => $course->id
        );
        
        return $this->connection->query('SELECT role FROM enrollment 
            WHERE %and', $params)->fetchSingle();
    }
    
    public function findAllUserIds(Course $course) {
        $ids = $this->connection->query('SELECT user_id FROM enrollment 
            WHERE [course_id] = %i', $course->id)->fetchAssoc('user_id');
        
        $keys = array_keys($ids);
        return $keys;
    }
}
