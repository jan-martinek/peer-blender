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
        foreach ($questions as $id => $question) {
            $questionText = $question->text;
            if ($question->type != 'plaintext') {
                $questionText .= "\n\n(" . $translator->translate('messages.unit.highlighting.' . $question->type) . ')';
            }

            $input = $questionsContainer->addTextarea(
                $id, 
                Html::el()->setHtml(Markdown::defaultTransform($questionText))
            );
            
            if ($question->type != 'plaintext') {
                $input->getControlPrototype()->class('highlight-' . $question->type);
            }
            
            if (isset($question->answer)) {
                $input->setValue($question->answer->text);
            } elseif ($question->prefill) {
                $input->setValue($question->prefill);
            }
        }
        
        $uploadLabel = $translator->translate('messages.unit.homeworkAttachment');
        $this->addUpload('attachment', $uploadLabel)
            ->addRule(
                Form::MAX_FILE_SIZE, 
                $uploadLabel, 
                $course->uploadMaxFilesizeKb * 1024
            )->setOption('description', $translator->translate(
                'messages.unit.homeworkAttachmentNote',
                NULL, 
                array('filesize' => $course->uploadMaxFilesizeKb)
            ));
        
        $submitLabel = $translator->translate('messages.unit.submitHomework');
        $this->addSubmit('submit', $submitLabel);
    }
}
