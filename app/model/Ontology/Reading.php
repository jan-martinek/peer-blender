<?php

namespace Model\Ontology;

class Reading
{
    use \Nette\SmartObject;

    public $source;
    public $text;

    public function __construct($data) 
    {
        $this->source = $data['filename'];
        $this->text = $data['data'];
    }
}
