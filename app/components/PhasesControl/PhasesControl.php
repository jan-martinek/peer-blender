<?php

namespace App\Components;

use Nette\Application\UI\Control;

class PhasesControl extends Control
{
    public function render(\Model\Ontology\UnitProduct $unit, $small = FALSE)
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/phases.latte');
        $template->unit = $unit;
        $template->small = $small;
        $template->render();
    }
}
