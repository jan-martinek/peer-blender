<?php

namespace Model\Generator;

use Nette;

class AttachmentGenerator extends Nette\Object implements IGenerator
{
    
    public function getPreface() 
    {
        return '';
    }

    public function getQuestions() 
    {           
        return array();
    }
    
    public function getRubrics() 
    {
        return array();
    }
    
}
