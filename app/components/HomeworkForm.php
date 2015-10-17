<?php

namespace App\Components;

use Nette\Application\UI\Form;
use Nette\Utils\Html;
use Michelf\Markdown;
use Model\Entity\Solution;
use DateTime;

class HomeworkForm extends Form
{   
    public function __construct($presenter, $course) 
    {    
        parent::__construct();
        
        $questionsContainer = $this->addContainer('questions');
        foreach ($presenter->questions as $id => $question) {
            $input = $questionsContainer->addTextarea($id, 
                Html::el()->setHtml(Markdown::defaultTransform($question->text))
            ); 
            
            if (isset($question->answer)) {
                $input->setValue($question->answer->text);
            }
        }
        
        $uploadLabel = $presenter->translator->translate('messages.unit.homeworkAttachment');
        $this->addUpload('attachment', $uploadLabel)
            ->addRule(
                Form::MAX_FILE_SIZE, 
                $uploadLabel, 
                $course->uploadMaxFilesizeKb * 1024
            )->setOption('description', $presenter->translator->translate(
                'messages.unit.homeworkAttachmentNote',
                NULL, 
                array('filesize' => $course->uploadMaxFilesizeKb)
            ));
        
        $submitLabel = $presenter->translator->translate('messages.unit.submitHomework');
        $this->addSubmit('submit', $submitLabel);
    }
}
