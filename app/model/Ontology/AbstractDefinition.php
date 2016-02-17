<?php

namespace Model\Ontology;

abstract class AbstractDefinition extends \Nette\Object implements IDefinition
{   
    /** @var CourseFactory */
    private $factory;
    
    
    /**
     * Creates question definition
     * @param array
     * @param CourseDefinition 
     */
    public function __construct($data, $factory) 
    {       
        $this->factory = $factory;
    }
}
