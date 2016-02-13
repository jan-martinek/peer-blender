<?php

namespace Model\Repository;

use Model\Entity\Course;

class MessageRepository extends Repository
{
    public function findLatest(Course $course, $limit = 20) 
    {
        $where = array(
            'course_id%i' => $course->id
        );
        
        $messages = $this->connection->select('*')
            ->from($this->getTable())
            ->where($where)
            ->limit($limit)
            ->orderBy('id DESC')->fetchAssoc('id');

        ksort($messages);
        
        return $this->createEntities($messages);
    }
    
    public function findRange(Course $course, $idFrom, $idTo, $limit = 100) 
    {
        $where = array(
            'course_id%i' => $course->id,
            '[id] >= %i' => $idFrom,
            '[id] <= %i' => $idTo
        );
        
        $query = $this->connection->select('*')
            ->from($this->getTable())
            ->where($where)
            ->limit($limit)
            ->orderBy('id DESC');
            
        return $this->createEntities($query->fetchAll());
    }
    
    public function findAllInCourse(Course $course, $offset = 0, $limit = 100) 
    {   
        $query = $this->connection->select('*')
            ->from($this->getTable())
            ->where(array('course_id%i' => $course->id))
            ->limit($limit)
            ->offset($offset)
            ->orderBy('id DESC');
            
        return $this->createEntities($query->fetchAll());
    }
}
