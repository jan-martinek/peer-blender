<?php

namespace Model;

use Nette;

class UploadStorage extends Nette\Object
{
    private $dir;
    private $path;

    public function __construct($dir, $path)
    {
        $this->dir = $dir;
        $this->path = $path;
    }

    public function getAbsolutePath($path) 
    {
    	return $this->dir . $path;
    }
 
 	public function getPath()
 	{
 		return $this->path;
 	}
}