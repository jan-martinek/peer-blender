<?php

namespace Model\Repository;

use Exception;
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
    public function getMyAssignment($unit, $student, $test = FALSE) 
    {
        if ($test) {
            return $this->generateAssignment($unit, $student, TRUE);
        }
        
        if ($assignment = $this->findByUnitAndUser($unit, $student)) {
            return $assignment;
        } else {
            return $this->generateAssignment($unit, $student);
        }
    }
    
    public function findByUnitAndUser($unit, $student) 
    {   
        $assignment = $this->connection->select('*')
            ->from($this->getTable())
            ->where(array('unit_id' => $unit->id, 'student_id%i' => $student->id));
              
        if ($assignment->fetch()) {
            return $this->createEntity($assignment->fetch());
        } else {
            return FALSE;
        }
    }
    
    private function generateAssignment($unit, $student, $test = FALSE) 
    {
        $generatorClassname = $unit->generator ? '\Model\Generator\\' . $unit->generator : '\Model\Generator\AttachmentGenerator';
        $generator = new $generatorClassname;
        
        $assignment = new \Model\Entity\Assignment;
        $assignment->preface = $generator->getPreface();
        $assignment->questionSet = $generator->getQuestions();
        if ($generator instanceof \Model\Generator\AttachmentGenerator) {
            $rubrics = array();
            foreach (explode("\n", $unit->rubrics) as $rubric) {
                $rubric = trim($rubric);
                
                if ($rubric) {
                    $rubrics[] = $rubric;    
                }
            }
            $assignment->rubricSet = $rubrics;
        } else {
            $assignment->rubricSet = $generator->getRubrics();
        }
        $assignment->unit = $unit;
        $assignment->generated_at = new DateTime;
        if (!$test) {
            $assignment->student = $student;
            $this->persist($assignment);    
        }
        
        return $assignment;
    }
}

class CourseRepository extends Repository
{   
    public function getReviewStats(\Model\Entity\Course $course) {
        $units = array();
        foreach ($course->units as $unit) {
            $units[] = $unit->id;
        }
        return $this->connection->query('SELECT solution.unit_id, count(*), AVG(score), std(score) FROM review
            JOIN solution ON solution.id = review.id
            WHERE unit_id IN %in', $units,
            'GROUP BY unit_id')->fetchAll();
    }
}

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

class FavoriteRepository extends Repository
{
    public function findByUserAndId($user, $entity, $entity_id) 
    {
        $where = array(
            'user_id%i' => $user->id,
            'entity%s' => $entity,
            'entity_id%i' => $entity_id   
        );
        
        $query = $this->connection->select('*')
            ->from($this->getTable())
            ->where($where);
        
        return $query->fetch();
    }
    
    public function findAllByScope($entity, array $entity_ids) 
    {
        $where = array(
            'entity%s' => $entity,
            'entity_id%in' => $entity_ids
        );
        
        $query = $this->connection->select('[entity_id], count(*) as [count]')
            ->from($this->getTable())
            ->groupBy('[entity_id]')
            ->where($where);
        
        return $query->fetchPairs('entity_id', 'count'); 
    }
      
    public function countFavoritesOfEntity($entity, $entity_id) 
    {
        $where = array(
            'entity%s' => $entity,
            'entity_id%i' => $entity_id   
        );
        
        $query = $this->connection->select('count(*)')
            ->from($this->getTable())
            ->where($where);
            
        return $query->fetchSingle();
    }
    
}

class MessageRepository extends Repository
{
    public function findLatest(\Model\Entity\Course $course, $limit = 20) 
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
    
    public function findRange(\Model\Entity\Course $course, $idFrom, $idTo, $limit = 100) 
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
    
    public function findAllInCourse(\Model\Entity\Course $course, $offset = 0, $limit = 100) 
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
    
    public function findUnfinishedReview($unit, $reviewer) {
        $query = $this->connection->query(
            'SELECT review.* FROM review 
            LEFT JOIN solution ON solution_id = solution.id 
            WHERE solution.unit_id = %i', $unit->id,
            'AND review.reviewed_by_id = %i', $reviewer->id,
            'AND (score IS NULL OR comments IS NULL OR review.submitted_at IS NULL)');
        
        if ($openedReview = $query->fetch()) {
            return $this->createEntity($openedReview);
        } else {
            return false;
        }
    }
    
    public function findByUnitAndReviewer($unit, $user) {
        $query = $this->connection->query(
            'SELECT review.* FROM review 
            LEFT JOIN solution ON solution_id = solution.id 
            WHERE solution.unit_id = %i', $unit->id,
            'AND review.reviewed_by_id = %i', $user->id,
            'ORDER BY opened_at');
        
        return $this->createEntities($query->fetchAll());
    }

    public function findByUnitAndUser($unit, $user) {
        $query = $this->connection->query(
            'SELECT review.* FROM review 
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
        $alreadyReviewedByMe = $this->connection->query(
            'SELECT solution.id AS id 
              FROM solution
              LEFT JOIN review ON solution.id = review.solution_id
              WHERE solution.unit_id = %i', $unit->id, 'AND reviewed_by_id = %i', $reviewer->id)->fetchAssoc('id');
        
        
        if (count($alreadyReviewedByMe) >= $unit->course->reviewCount) {
            throw new ReviewLimitReachedException();
            return FALSE;
        }
        
        $reviewStats = $this->connection->query(
            'SELECT solution.id, count(*) as reviewCount
              FROM solution
              LEFT JOIN review ON solution.id = review.solution_id
              WHERE solution.unit_id = %i', $unit->id,
              'AND solution.id NOT IN %in', count($alreadyReviewedByMe) ? array_keys($alreadyReviewedByMe) : array(0),
              'AND solution.user_id != %i', $reviewer->id,
            'GROUP BY solution.id')->fetchAssoc('reviewCount,id');
        
        if (!count($reviewStats)) {
            throw new SolutionToReviewNotFoundException();
            return FALSE;
        }
        
        $lowestReviewCount = min(array_keys($reviewStats));
        
        if ($lowestReviewCount >= $unit->course->reviewCount) {
            throw new SolutionToReviewNotFoundException();
            return FALSE;
        }
        
        $randomlyPickedSolutionId = array_rand($reviewStats[$lowestReviewCount]);
        
        return $this->find($randomlyPickedSolutionId);
    }
    
}

class SolutionToReviewNotFoundException extends Exception {
    
}

class ReviewLimitReachedException extends Exception {
    
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

class LogRepository extends \LeanMapper\Repository
{   
}
