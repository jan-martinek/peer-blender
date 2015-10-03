<?php

namespace Model\Generator;

use Nette;

interface IQuestionset
{   
    /**
     * Returns question in markdown.
     * @return string
     */
    function getQuestions($count);
    
    /**
     * Returns classification of the question in Revised 
     * Bloom's Taxonomy of Education Objectives
     * @return bool
     */
    function setBloom($objective);
    
    /**
     * Returns classification of the question in Revised 
     * Bloom's Taxonomy of Education Objectives
     * @return string
     */
    function getBloom();
    
}
