<?php

namespace App\Components;

use Nette\Application\UI\Form;
use Nette\Utils\Html;
use Michelf\Markdown;
use Model\Entity\Review;
use DateTime;

class ReviewForm extends Form
{
    
    private $review;
    private $reviewRepository;
    private $solution;
    
    public function __construct($review, $reviewRepository, $translator) 
    {    
        parent::__construct();
        
        $this->review = $review;
        $this->solution = $review->solution;
        $this->reviewRepository = $reviewRepository;
        
        $rubrics = $this->solution->assignment->rubricSet;
        $rubricsContainer = $this->addContainer('rubrics');
        foreach ($rubrics as $id => $rubric) {
            $rubricsContainer->addTextarea($id, 
                Html::el()->setHtml(Markdown::defaultTransform($rubric))
            )->setRequired($translator->translate('messages.review.verbalAsssessmentCompulsory'))
                ->addRule(
                    Form::MIN_LENGTH, 
                    $translator->translate('messages.review.assessmentMinimumLength', NULL, array('count' => 20)),
                    20);
        }
        
        $options = array();
        for ($i = 0; $i <= 3; $i++) {
            $options[$i] = $translator->translate('messages.review.score.' . $i);
        }
        
        $scoreLabel = $translator->translate('messages.review.score.title');
        $scorePlaceholder = $translator->translate('messages.review.useRubricsWhileChoosingScore');
        $this->addSelect('score', $scoreLabel, $options)->setPrompt($scorePlaceholder);
        
        $notesLabel = $translator->translate('messages.review.notes');
        $this->addTextarea('notes', $notesLabel);
        
        $submitLabel = $translator->translate('messages.review.submit');
        $this->addSubmit('submit', $submitLabel);
    }
}
