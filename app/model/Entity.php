<?php

namespace Model\Entity;

class Entity extends \LeanMapper\Entity 
{
    public function getConventionalName() 
    {
        $name = array_slice(explode('\\', get_class($this)), -1, 1);
        return $name[0];
    }
}
