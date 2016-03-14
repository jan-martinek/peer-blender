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
    
    public function __construct($review, $reviewRepository, $rubrics, $translator) 
    {    
        parent::__construct();
        
        $this->review = $review;
        $this->solution = $review->solution;
        $this->reviewRepository = $reviewRepository;
        
        $rubricsContainer = $this->addContainer('rubrics');
        foreach ($rubrics as $id => $rubric) {
            if ($rubric instanceof \Model\Ontology\DefaultRubric) {
                $options = array();
                for ($score = 0; $score <= 3; $score++) {
                    $options[$score] = $translator->translate('messages.review.score.' . $score);
                }
                
                $scoreLabel = $translator->translate('messages.review.score.title');
                $scorePlaceholder = $translator->translate('messages.review.useRubricsWhileChoosingScore');
                $rubricsContainer->addRadioList($id, $scoreLabel, $options);
            } elseif ($rubric instanceof \Model\Ontology\Rubric) {
                $options = array();
                foreach ($rubric->scale as $score => $description) {
                    $options[$score] = Html::el()->setHtml(strip_tags(Markdown::defaultTransform($description), '<a><strong><em>'));
                }
                
                $scoreLabel = $rubric->metric;
                $scorePlaceholder = $translator->translate('messages.review.useRubricsWhileChoosingScore');
                $rubricsContainer->addRadioList($id, $scoreLabel, $options);
            } elseif ($rubric instanceof \Model\Ontology\Comment) {
                $rubricsContainer->addTextarea($id, 
                    Html::el()->setHtml(Markdown::defaultTransform($rubric->instructions))
                )->setRequired($translator->translate('messages.review.verbalAsssessmentCompulsory'))
                    ->addRule(
                        Form::MIN_LENGTH, 
                        $translator->translate('messages.review.assessmentMinimumLength', NULL, array('count' => 20)),
                        20);
            }
        }
        
        $notesLabel = $translator->translate('messages.review.notes');
        $this->addTextarea('notes', $notesLabel);
        
        $completeLabel = $translator->translate('messages.review.complete');
        $this->addCheckbox('complete', $completeLabel);
        
        $submitLabel = $translator->translate('messages.review.submit');
        $this->addSubmit('submit', $submitLabel);
    }
}
