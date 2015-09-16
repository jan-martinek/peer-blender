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
        
        $rubrics = unserialize($this->solution->assignment->rubrics);
        $rubricsContainer = $this->addContainer('rubrics');
        foreach ($rubrics as $id => $rubric) {
            $rubricsContainer->addTextarea($id, 
                Html::el()->setHtml(Markdown::defaultTransform($rubric))
            );
        }
        
        $options = array();
        for ($i = 0; $i <= 3; $i++) {
            $options[$i] = $translator->translate('messages.review.score.' . $i);
        }
        
        $scoreLabel = $translator->translate('messages.review.score.title');
        $scorePlaceholder = $translator->translate('messages.review.score.placeholder');
        $this->addSelect('score', $scoreLabel, $options)->setPrompt($scorePlaceholder);
        
        $commentsLabel = $translator->translate('messages.review.comments');
        $this->addTextarea('comments', $commentsLabel);
        
        $submitLabel = $translator->translate('messages.review.submit');
        $this->addSubmit('submit', $submitLabel);
    }
}
