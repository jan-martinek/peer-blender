<?php

namespace App\Components;

use Nette\Application\UI\Control;

class ReviewsControl extends Control
{
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
    }	
	
    public function render(Array $reviews, $showIncomplete = FALSE)
    {
        $template = $this->template;
        $this->setTemplateFilters($template);
        $template->setFile(__DIR__ . '/reviews.latte');
        $template->showIncomplete = $showIncomplete;
        $template->reviews = $reviews;
        $template->render();
    }
}
