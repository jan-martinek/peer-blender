<?php

namespace Model\Generator;

use Nette;

interface IGenerator
{

	/**
     * Returns introduction to the homework.
     * @return string
     */
	function getPreface();

	/**
     * Returns array of questions and instructions.
     * @return array
     */
    function getQuestions();
    
    /**
     * Returns array of rubrics used in evaluation.
     * @return array
     */
    function getRubrics();
    
}
