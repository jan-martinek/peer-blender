<?php

namespace Model\Generator;

use Nette;

class TestGenerator extends Nette\Object implements IGenerator
{
    
    
    public function getPreface() 
    {
        return 'This is a fairly easy task to begin with.';
    }
    

    public function getQuestions() 
    {
        return array(
            'What is *love*?',
            'Find an essay on "sad sharks" on the internet and sum it up in three sentences.'
        );
    }
    
    public function getRubrics() 
    {
        return array(
            'Is the answer truthful?',
            'Is the answer emotionally fulfilling?',
            'Are there any references to *\'Night at Roxbury\'* in the answer?',
        );
    }
    
}
