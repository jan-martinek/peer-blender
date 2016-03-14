<?php

namespace App\Components;

use Nette\Application\UI\Control;
use Michelf\Markdown;

class QuestionsControl extends Control
{   
    private $uploadStorage;
    
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
            if ($s == 0) {
                return '—';
            } else {
                return str_repeat('★', $s);    
            }
        });
    }	
	
    public function render(\Model\Ontology\AssignmentProduct $assignment, \Model\Entity\Solution $solution = null, \App\Components\HomeworkForm $form = null)
    {
        $template = $this->template;
        $this->setTemplateFilters($template);
        $template->setFile(__DIR__ . '/questions.latte');
        $template->uploadPath = $this->uploadStorage->path;
        $template->assignment = $assignment;
        $template->solution = $solution;
        $template->form = $form;
        $template->render();
    }
}
