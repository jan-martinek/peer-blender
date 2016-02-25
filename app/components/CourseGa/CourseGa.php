<?php

namespace App\Components;

use Nette\Application\UI\Control;

class CourseGaControl extends Control
{		
    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/courseGa.latte');
        if (isset($this->presenter->courseRegistry->course)) {
            $template->gaCode = $this->presenter->courseRegistry->course->gaCode;    
        }
        $template->render();
    }
}
