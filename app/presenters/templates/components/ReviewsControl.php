<?php

namespace App\Components;

use Nette\Application\UI\Control;

class ReviewsControl extends Control
{
    public function render(Array $reviews)
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/reviews.latte');
        $template->reviews = $reviews;
        $template->render();
    }
}
