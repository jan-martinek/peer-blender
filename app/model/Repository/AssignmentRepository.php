<?php

namespace Model\Repository;

use Model\CourseDefinition;
use Model\Entity\Assignment;
use Model\Entity\Unit;
use Model\Entity\User;
use Model\Entity\Question;

use Exception;
use DateTime;

class AssignmentRepository extends Repository
{   
    public function getMyAssignment(Unit $unit, User $student, $questionRepository, CourseDefinition $courseDefinition) 
    {
        if ($assignment = $this->findByUnitAndUser($unit, $student)) {
            return $assignment;
        } else {
            return $this->generateAssignment($unit, $student, $questionRepository, $courseDefinition);       
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
    
    private function generateAssignment(Unit $unit, User $student, QuestionRepository $questionRepository, CourseDefinition $courseDefinition) 
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
    
    private function generateQuestions($definitions, $inherit = array()) 
    {
        $questionSet = array();
        foreach ($definitions as $defId => $def) {
            $def = array_merge($inherit, $def);
            if (!isset($def['id'])) $def['id'] = $defId;
            
            $count = isset($def['count']) ? $def['count'] : 1;
            
            $questions = array();    
            if (isset($def['questions'][0]['questions'])) {
                $questions = array_merge(
                    $questions, 
                    $this->generateQuestions($def['questions'], $def)
                );
            } else {
                $question = new Question;
                $question->definition = (object) $def;
                $question->definition_id = $def['id'];
                $question->generateFromDefinition();
                $questions[] = $question;
                
                if ($count > 1) {
                    for($i = 1; $i < $count; $i++) {
                        $question = unserialize(serialize($question));
                        $questions[] = $question->variate();
                    }
                }
            }

            shuffle($questions);
            for ($i = 0; $i < $count; $i++) {
                $questionSet[] = $questions[$i];
            }
        }
        
        return $questionSet;
    }
}
