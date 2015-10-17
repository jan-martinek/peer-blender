<?php

namespace App\Presenters;

use DateTime;
use Model\Entity\Log;
use Model\Entity\Solution;
use Nette\Utils\Strings;

/**
 * Unit presenter.
 */
class MigrationPresenter extends BasePresenter
{   
    /** @var \Model\Repository\AssignmentRepository @inject */
    public $assignmentRepository;
    
    /** @var \Model\Repository\QuestionRepository @inject */
    public $questionRepository;
    
    /** @var \Model\Repository\AnswerRepository @inject */
    public $answerRepository;
    
    
    public function actionDefault() 
    {
        $as = $this->assignmentRepository->findAll();
        
        foreach ($as as $assignment) {
            $set = $assignment->questionSet;
            if (count($set)) {
                $answerSet = $assignment->solution ? $assignment->solution->answerSet : null;
            
                foreach ($set as $r => $q) {
                    $question = new \Model\Entity\Question;
                    $question->text = $q;
                    $question->order = $r; 
                    $question->type = 'plaintext';
                    $question->assignment = $assignment;
                    $this->questionRepository->persist($question);
                    if ($a = $answerSet[$question->order]) {
                        $answer = new \Model\Entity\Answer;
                        $answer->text = $a;
                        $answer->solution = $assignment->solution;
                        $answer->question = $question;
                        $this->answerRepository->persist($answer);
                    }
                }                
            }
        }
        
        die;
    }
}
