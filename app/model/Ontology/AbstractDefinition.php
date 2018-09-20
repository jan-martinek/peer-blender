<?php

namespace Model\Ontology;

abstract class AbstractDefinition implements IDefinition
{   
    use \Nette\SmartObject;

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
