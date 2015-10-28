<?php

namespace Model\Repository;

class UnitRepository extends Repository
{
    public function findByCourseId($courseId, $published = true)
    {
        $where = array();
        $where['course_id%i'] = $courseId;
        if ($published) {
            $where[] = array('published_since <= %sql', 'CURDATE()');
        }
        
        $query = $this->connection->select('*')
            ->from($this->getTable())
            ->where($where)
            ->orderBy('published_since');        
        return $this->createEntities($query->fetchAll());
    }
}
