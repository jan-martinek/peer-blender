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
    
    public function setTemplateFilters($template) {
    	//markdown
        $template->addFilter('md', function ($s) {
            return \Michelf\Markdown::defaultTransform($s);
        });
        //inline markdown
        $template->addFilter('imd', function ($s) {
            return strip_tags(Markdown::defaultTransform($s), '<a><strong><em>');
        });
    }	
	
    public function render(\Model\Entity\Assignment $assignment, \Model\Entity\Solution $solution)
    {
        $template = $this->template;
        $this->setTemplateFilters($template);
        $template->setFile(__DIR__ . '/questions.latte');
        $template->uploadPath = $this->uploadStorage->path;
        $template->assignment = $assignment;
        $template->solution = $solution;
        $template->render();
    }
}
