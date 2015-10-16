<?php

namespace App\Components;

interface IChatControlFactory
{
    /** @return ChatControl */
    function create();
}