<?php

namespace Model\Repository;

use Model\Generator\AttachmentGenerator;
use Model\Entity\Assignment;
use Model\Entity\Unit;
use Model\Entity\User;
use Exception;
use DateTime;

class AssignmentRepository extends Repository
{   
    public function getMyAssignment(Unit $unit, User $student, $questionRepository) 
    {
        if ($assignment = $this->findByUnitAndUser($unit, $student)) {
            return $assignment;
        } else {
            return $this->generateAssignment($unit, $student, $questionRepository);
        }
    }
    
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
    
    private function generateAssignment(Unit $unit, User $student, QuestionRepository $questionRepository) 
    {
        $generatorClassname = '\Model\Generator\\' . $unit->generator;
        $generator = new $generatorClassname;
        $assignment = new Assignment;
        
        $assignment->preface = $generator->getPreface();
        if ($generator instanceof AttachmentGenerator) {
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
        $assignment->student = $student;
        $this->persist($assignment);    
        
        $questions = $generator->getQuestions();
        $i = 0;
        foreach ($questions as $question) {
            $question->assignment = $assignment;
            $question->order = $i++;
            $questionRepository->persist($question);
        }
        
        return $assignment;
    }
}
