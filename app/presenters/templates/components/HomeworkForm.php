<?php

namespace App\Components;

use Nette\Application\UI\Form;
use Nette\Utils\Html;
use Michelf\Markdown;
use Model\Entity\Solution;
use DateTime;

class HomeworkForm extends Form
{
    private $solutionRepository;
    private $assignment;
    
    public function __construct($assignment, $questions, $solutionRepository, $translator) 
    {    
        parent::__construct();
        
        $this->assignment = $assignment;
        $this->solutionRepository = $solutionRepository;
        
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
        
        $this->onSuccess[] = array($this, 'formSucceeded');
    }
    
    public function formSucceeded(HomeworkForm $form, $values) 
    {
        
        if ($solution = $this->assignment->solution) {
            $solution->edited_at = new DateTime;
            $solution->answer = serialize((array) $values->questions);
            $solution->attachment = 'TODO';
            $this->solutionRepository->persist($solution);
        } else {
            $solution = new Solution;
            $solution->unit = $this->assignment->unit;
            $solution->assignment = $this->assignment;
            $solution->user = $this->assignment->student;
            $solution->submitted_at = new DateTime;
            $solution->edited_at = new DateTime;
            $solution->answer = serialize((array) $values->questions);
            $solution->attachment = 'TODO';
            $this->solutionRepository->persist($solution);            
        }

        $this->getPresenter()->redirect('this');
    }
}
