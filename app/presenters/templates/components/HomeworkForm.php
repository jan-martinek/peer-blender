<?php

namespace App\Components;

use Nette\Application\UI\Form;
use Nette\Utils\Html;
use Michelf\Markdown;
use Model\Entity\Solution;
use DateTime;

class HomeworkForm extends Form
{   
    public function __construct($questions, $translator) 
    {    
        parent::__construct();
        
        $questionsContainer = $this->addContainer('questions');
        foreach ($questions as $id => $question) {
            $questionsContainer->addTextarea($id, 
                Html::el()->setHtml(Markdown::defaultTransform($question))
            ); 
        }
        
        $uploadLabel = $translator->translate('messages.unit.homeworkAttachment') 
            . ' ' . $translator->translate('messages.unit.homeworkAttachmentNote');
        $this->addUpload('attachment', $uploadLabel);
        
        $submitLabel = $translator->translate('messages.unit.submitHomework');
        $this->addSubmit('submit', $submitLabel);
    }
}
