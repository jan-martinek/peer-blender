<?php

namespace Model\Ontology;

interface IDefinition
{
    /** 
     * @param array
     * @param \Model\Ontology\CourseFactory
     */
    public function __construct($data, $factory);
    
    
    /**
     * Assembles a new variation.
     * @return \Model\Entity\Entity
     */
    public function assemble();
    
    
    /**
     * Produces a product from entity and definition.
     * @return \Model\Ontology\AbstractProduct
     */
    public function produce($entity);
}
