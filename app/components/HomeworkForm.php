<?php

namespace App\Components;

use Nette\Application\UI\Form;
use Nette\Utils\Html;
use Michelf\Markdown;
use Model\Entity\Answer;
use Model\Entity\Solution;
use DateTime;

class HomeworkForm extends Form
{   
    private $presenter;
    
    public function __construct($presenter, $questions) 
    {    
        parent::__construct();
        
        $this->presenter = $presenter;
        $course = $presenter->courseInfo->course;
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
                    $input = $questionsContainer->addUpload($id, $label)
                        ->addRule(
                            Form::MAX_FILE_SIZE, 
                            $translator->translate(
                                'messages.unit.homeworkAttachmentNote', 
                                NULL, 
                                array('filesize' => $maxKb)
                            ), 
                            $maxKb * 1024
                    );
                    break;
                default:
                    $input = $questionsContainer->addTextarea($id, $label);                        
                    if ($question->isHighlightingAvailable()) {
                        $input->getControlPrototype()->class('highlight-' . $question->input);
                    }
            }
            
            if (isset($question->answer)) {
                $input->setValue($question->answer->text);
            } elseif ($question->prefill) {
                $input->setValue($question->prefill);
            }
            
            $comments = $commentsContainer->addTextarea($id)
                ->setAttribute('placeholder', 
                    $translator->translate('messages.solution.addComments')
                );
            if (isset($question->answer->comments)) {
                $comments->setValue($question->answer->comments);
            }
        }    
        
        $submitLabel = $translator->translate('messages.unit.submitHomework');
        $this->addSubmit('submit', $submitLabel);
        $this->onSuccess[] = array($this, 'formSucceeded');
    }
    
    public function formSucceeded(HomeworkForm $form, $values) 
    {
        $courseInfo = $this->presenter->courseInfo;
        
        if ($solution = $courseInfo->solution) {
            $event = 'edit';
        } else {
            $event = 'create';
            $solution = new Solution;
            $solution->unit = $courseInfo->unit;
            $solution->assignment = $courseInfo->assignment;
            $solution->user = $this->presenter->userInfo;
            $solution->submitted_at = new DateTime;
        }
        
        $solution->edited_at = new DateTime;
        $this->presenter->solutionRepository->persist($solution);
        
        $this->saveAnswers($courseInfo->assignment->questions, $values->questions, $values->comments);
        
        $backToButton = '';
        $httpData = $form->getHttpData();
        foreach (array_keys($httpData) as $k) {
            if (preg_match('/^quick-save-button-[0-9]+$/', $k)) {
                 $backToButton = '#' . $k;
            }
        }
        
        $this->presenter->logEvent($solution, $event);
        $this->presenter->redirect('this' . $backToButton);
    }
    
    public function saveAnswers($questions, $values, $comments) 
    {
        $courseInfo = $this->presenter->courseInfo;
        
        foreach ($questions as $order => $question) {
            if (isset($question->answer)) {
                $answer = $question->answer; 
            } else {
                $answer = new Answer;
                $answer->solution = $question->assignment->solution;
                $answer->question = $question;
                $answer->text = null;
            }
            
            if ($question->input == 'file') {
                $answer->text = $this->saveHomeworkFile(
                    $courseInfo->course->id,
                    $courseInfo->unit->id,
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
    private function saveHomeworkFile($courseId, $unitId, $userId, $file, $current) 
    {
        if ($file->isOK()) {
            $this->removeHomeworkFile($current);   
            $path = "/course-$courseId/homeworks/unit-$unitId/user-$userId/";
            return $this->presenter->uploadStorage->moveUploadedFile($file, $path);
        } else {
            return $current;
        }
    }
    
    private function removeHomeworkFile($filename) 
    {
        $absoluteFilename = $this->presenter->uploadStorage->getAbsolutePath($filename);
        
        if (file_exists($absoluteFilename) && is_file($absoluteFilename)) {
            return unlink($absoluteFilename);    
        } 
    }    
}
