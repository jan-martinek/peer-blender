<?php

namespace App\Components;

use Nette\Application\UI\Form;
use Nette\Utils\Html;
use Michelf\Markdown;
use Model\Entity\Answer;
use Model\Entity\Solution;
use DateTime;

class AssignmentForm extends Form
{   
    private $presenter;
    
    public function __construct($presenter, $questions) 
    {    
        parent::__construct();
        
        $this->presenter = $presenter;
        $course = $presenter->courseRegistry->course;
        $translator = $presenter->translator;
        
        $questionsContainer = $this->addContainer('questions');
        $commentsContainer = $this->addContainer('comments');
        foreach ($questions as $id => $question) {
            $label = Html::el()->setHtml(
                Markdown::defaultTransform($question->text)
            );
            
            switch ($question->input) {
                case 'file':
                    $maxKb = $course->uploadMaxFilesizeKb;
                    $input = $questionsContainer
                        ->addUpload($id, $label)
                        ->addRule(
                            Form::MAX_FILE_SIZE, 
                            $translator->translate(
                                'messages.unit.assignmentAttachmentNote', 
                                NULL, 
                                array('filesize' => $maxKb)
                            ), 
                            $maxKb * 1024)
                        ->setOption('description', $translator->translate(
                                'messages.unit.assignmentAttachmentNote', 
                                NULL, 
                                array('filesize' => $maxKb)
                            ));
                    break;
                default:
                    $input = $questionsContainer->addTextarea($id, $label);                        
                    if ($question->isHighlightingAvailable()) {
                        $input->getControlPrototype()->class('highlight-' . $question->input);
                    }
            }
            
            if (isset($question->entity->answer)) {
                $input->setValue($question->entity->answer->text);
            } elseif ($question->prefill) {
                $input->setValue($question->prefill);
            }
            
            $comments = $commentsContainer->addTextarea($id)
                ->setAttribute('placeholder', 
                    $translator->translate('messages.solution.addComments')
                );
            if (isset($question->entity->answer->comments)) {
                $comments->setValue($question->entity->answer->comments);
            }
        }    
        
        $submitLabel = $translator->translate('messages.unit.submitAssignment');
        $this->addSubmit('submit', $submitLabel);
        $this->onSuccess[] = array($this, 'formSucceeded');
    }
    
    public function formSucceeded(AssignmentForm $form, $values) 
    {
        $courseRegistry = $this->presenter->courseRegistry;
        
        if ($solution = $courseRegistry->solution) {
            $event = 'edit';
        } else {
            $event = 'create';
            $solution = new Solution;
            $solution->unit = $courseRegistry->unit;
            $solution->assignment = $courseRegistry->assignment;
            $solution->user = $this->presenter->userInfo;
            $solution->submitted_at = new DateTime;
        }
        
        $solution->edited_at = new DateTime;
        $this->presenter->solutionRepository->persist($solution);
        
        $this->saveAnswers($courseRegistry->assignment->questions, $values->questions, $values->comments);
        $this->presenter->logEvent($solution, $event);
        
        if ($this->presenter->isAjax()) {
            $this->presenter->flashMessage($this->presenter->translator->translate('messages.solution.saved'));
            $this->presenter->invalidateControl('flashMessages');
            $this->presenter->invalidateControl('formInfo');
            foreach ($form['questions']->getControls() as $q) {
                if ($q instanceof \Nette\Forms\Controls\UploadControl) {
                    $this->presenter['questionsRenderer']->invalidateControl('question-' . $q->name);
                }
            }
            $this->presenter['questionsRenderer']->invalidateControl('assignmentQuestions');
        } else {
            $this->presenter->redirect('this');
        }
    }
    
    public function saveAnswers($questions, $values, $comments) 
    {
        $courseRegistry = $this->presenter->courseRegistry;
        
        foreach ($questions as $order => $question) {
            if (isset($question->answer)) {
                $answer = $question->answer; 
            } else {
                $answer = new Answer;
                $answer->solution = $question->assignment->solution;
                $answer->question = $question;
                $answer->text = null;
            }
            
            $questionProduct = $this->presenter->produce($question);
            if ($questionProduct->input == 'file') {
                $answer->text = $this->saveAssignmentFile(
                    $order + 1,
                    $courseRegistry->course->id,
                    $courseRegistry->unit->id,
                    $this->presenter->user->id,
                    $values[$order],
                    $answer->text
                );
            } else {
                $answer->text = $values[$order];    
            }
            
            if ($comments[$order] != '') {
                $answer->comments = $comments[$order];
            }
            
            $this->presenter->answerRepository->persist($answer);
        }
    }
    
    /**
     * @return string uploaded filename
     */
    private function saveAssignmentFile($questionId, $courseId, $unitId, $userId, $file, $current) 
    {
        if ($file->isOK()) {
            $this->removeAssignmentFile($current);   
            $path = "/course-$courseId/assignments/unit-$unitId/question-$questionId/user-$userId/";
            return $this->presenter->uploadStorage->moveUploadedFile($file, $path);
        } else {
            return $current;
        }
    }
    
    private function removeAssignmentFile($filename) 
    {
        $absoluteFilename = $this->presenter->uploadStorage->getAbsolutePath($filename);
        
        if (file_exists($absoluteFilename) && is_file($absoluteFilename)) {
            return unlink($absoluteFilename);    
        } 
    }    
}
