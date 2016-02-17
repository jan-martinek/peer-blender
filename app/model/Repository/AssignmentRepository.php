<?php

namespace Model\Repository;

use Model\Ontology;
use Model\Entity\Assignment;
use Model\Entity\Unit;
use Model\Entity\User;
use Model\Entity\Question;

use Exception;
use DateTime;

class AssignmentRepository extends Repository
{   
    public function findByUnitAndUser(Unit $unit, User $student) 
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
    
    private function produceAssignment(Unit $unit, User $student, QuestionRepository $questionRepository, CourseDefinition $courseDefinition) 
    {
        $assignment = new Assignment;
        
        $assignment->unit = $unit;
        $assignment->generated_at = new DateTime;
        $assignment->student = $student;
        $this->persist($assignment);    
        
        $questions = $this->generateQuestions(
            $courseDefinition->get($unit, CourseDefinition::QUESTIONS)
        );
        
        foreach ($questions as $i => $question) {
            $question->order = $i + 1;
            $question->assignment = $assignment;
            $questionRepository->persist($question);
        }
        
        return $assignment;
    }
}
