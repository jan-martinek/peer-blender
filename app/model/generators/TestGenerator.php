<?php

namespace Model\Generator;

use Nette;

class TestGenerator extends Nette\Object implements IGenerator
{

    public function getQuestions() {
        return array(
            'What is *love*?'
        );
    }
    
    public function getRubrics() {
        return array(
            'Is the answer truthful?',
            'Is the answer emotionally fulfilling?',
            'Are there any references to *\'Night at Roxbury\'* in the answer?',
        );
    }
    
}
