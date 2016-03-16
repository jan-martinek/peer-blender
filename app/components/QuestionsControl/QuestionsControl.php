<?php

namespace App\Components;

use Nette\Application\UI\Control;
use Michelf\Markdown;

class QuestionsControl extends Control
{   
    private $uploadStorage;
    public $assignment;
    public $solution;
    public $form;
    public $lateEdits = FALSE;
    
    public function __construct(\Model\UploadStorage $storage) 
    {
        $this->uploadStorage = $storage;
    }
    
    public function setTemplateFilters($template) 
    {
    	//markdown
        $template->addFilter('md', function ($s) {
            return \Michelf\Markdown::defaultTransform($s);
        });
        //inline markdown
        $template->addFilter('imd', function ($s) {
            return strip_tags(Markdown::defaultTransform($s), '<a><strong><em>');
        });
        //stars
        $this->template->addFilter('stars', function ($s) {
            return \App\Components\ReviewForm::renderRatingStars($s);
        });
    }	
	
    public function render(\Model\Ontology\AssignmentProduct $assignment = null, \Model\Entity\Solution $solution = null, \App\Components\AssignmentForm $form = null)
    {
        $template = $this->template;
        $this->setTemplateFilters($template);
        $template->setFile(__DIR__ . '/questions.latte');
        $template->uploadPath = $this->uploadStorage->path;
        $template->assignment = $assignment ? $assignment : $this->assignment;
        $template->solution = $solution ? $solution : $this->solution;
        $template->lateEdits = $this->lateEdits;
        $template->form = $form ? $form : $this->form;
        $template->render();
    }
}
