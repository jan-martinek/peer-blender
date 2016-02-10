<?php

namespace App\Components;

use Nette\Application\UI\Form;
use Nette\Utils\Html;
use Michelf\Markdown;
use Model\Entity\Solution;
use DateTime;

class HomeworkForm extends Form
{   
    public function __construct($course, $questions, $translator) 
    {    
        parent::__construct();
        
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
                        ->addRule(Form::MAX_FILE_SIZE, $label, $maxKb * 1024)
                        ->setOption('description', $translator->translate(
                            'messages.unit.homeworkAttachmentNote',
                            NULL, array('filesize' => $maxKb)
                        )
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
    }
}
