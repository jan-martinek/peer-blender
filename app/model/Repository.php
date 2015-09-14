<?php

namespace Model\Repository;

use DateTime;

abstract class Repository extends \LeanMapper\Repository
{
    public function find($id)
    {
        $row = $this->connection->select('*')
            ->from($this->getTable())
            ->where('id = %i', $id)
            ->fetch();

        if ($row === false) {
            throw new \Exception('Entity was not found.');
        }

        return $this->createEntity($row);
    }

    public function findAll($orderBy = null)
    {
        $query = $this->connection->select('*')
                ->from($this->getTable());
        if ($orderBy) {
            $query->orderBy($orderBy);
        }

        return $this->createEntities($query->fetchAll());
    }
}

class AssignmentRepository extends Repository
{
    public function getMyAssignment($unit, $student) 
    {
        $assignment = $this->connection->select('*')
            ->from($this->getTable())
            ->where(array('unit_id' => $unit->id, 'student_id%i' => $student->id));
            
            
        if ($assignment->fetch()) {
            return $this->createEntity($assignment->fetch());
        } else {
            return $this->generateAssignment($unit, $student);
        }
    }
    
    private function generateAssignment($unit, $student) 
    {
        $generatorClassname = '\Model\Generator\\' . $unit->generator;
        $generator = new $generatorClassname;
        
        $assignment = new \Model\Entity\Assignment;
        $assignment->preface = $generator->getPreface();
        $assignment->questions = serialize($generator->getQuestions());
        $assignment->rubrics = serialize($generator->getRubrics());
        $assignment->unit = $unit;
        $assignment->student = $student;
        $assignment->generated_at = new DateTime;
        $this->persist($assignment);
        
        return $assignment;
    }
}

class CourseRepository extends Repository
{
}

class EnrollmentRepository extends Repository
{
}

class ObjectionRepository extends Repository
{
}

class ReviewRepository extends Repository
{
    public function createReview($solution, $reviewer) 
    {
        $review = new \Model\Entity\Review;
        $review->solution = $solution;
        $review->reviewed_by = $reviewer;
        $review->opened_at = new DateTime;
        $this->persist($review);
        
        return $review;
    }    
    
    public function findUnfinishedReview($reviewer) {
        $query = $this->connection->query("SELECT * FROM", $this->getTable(),  
            "WHERE score IS NULL OR comments IS NULL OR submitted_at IS NULL");
        
        if ($openedReview = $query->fetch()) {
            return $this->createEntity($openedReview);
        } else {
            return false;
        }
    }
    
    public function findByUnitAndReviewer($unit, $user) {
        $query = $this->connection->query(
            'SELECT * FROM review 
            LEFT JOIN solution ON solution_id = solution.id 
            WHERE solution.unit_id = %i', $unit->id,
            'AND review.reviewed_by_id = %i', $user->id,
            'ORDER BY opened_at');
        
        return $this->createEntities($query->fetchAll());
    }

    public function findByUnitAndUser($unit, $user) {
        $query = $this->connection->query(
            'SELECT * FROM review 
            LEFT JOIN solution ON solution_id = solution.id 
            WHERE solution.unit_id = %i', $unit->id,
            'AND review.reviewed_by_id = %i', $user->id,
            'ORDER BY opened_at');
        
        return $this->createEntities($query->fetchAll());
    }    
}

class SolutionRepository extends Repository
{
    public function findSolutionToReview($unit, $reviewer) 
    {
        $reviewStats = $this->connection->query(
            'SELECT solution.id, count(review.id) as reviewCount
              FROM solution
              LEFT JOIN review ON solution.id = review.solution_id
              WHERE solution.unit_id = %i', $unit->id, 
            'GROUP BY review.id')->fetchAssoc('reviewCount,id');
        
        $lowestReviewCount = min(array_keys($reviewStats));
        
        if ($lowestReviewCount >= $unit->course->reviewCount) {
            // reviewsSaturated
            return false;
        }
        
        $randomlyPickedSolutionId = array_rand($reviewStats[$lowestReviewCount]);
        
        return $this->find($randomlyPickedSolutionId);
    }
    
}

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
