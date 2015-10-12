<?php

namespace App\Components;

interface IQuestionsControlFactory
{
    /** @return QuestionsControl */
    function create();
}