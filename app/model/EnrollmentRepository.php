<?php

namespace Model\Repository;

class EnrollmentRepository extends Repository
{
    public function getRoleInCourse($user, $course) {
        $params = array(
            'user_id%i' => $user->id,
            'course_id%i' => $course->id
        );
        
        return $this->connection->query('SELECT role FROM enrollment 
            WHERE %and', $params)->fetchSingle();
    }
    
    public function findAllUserIds($course) {
        $ids = $this->connection->query('SELECT user_id FROM enrollment 
            WHERE [course_id] = %i', $course->id)->fetchAssoc('user_id');
        
        $keys = array_keys($ids);
        return $keys;
    }
}
