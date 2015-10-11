<?php

namespace App\Components;

use Nette\Application\UI\Control;

class PhasesControl extends Control
{
    public function render(\Model\Entity\Unit $unit)
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/phases.latte');
        $template->unit = $unit;
        $template->render();
    }
}
