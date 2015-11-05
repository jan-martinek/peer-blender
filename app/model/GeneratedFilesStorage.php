<?php

namespace Model;

use Nette;
use Nette\Utils\Strings;

class GeneratedFilesStorage extends Storage
{
    public function save($content, $filename, $ext, $path)
    {
        $filename = Strings::webalize($filename) . '.' . Strings::webalize($ext);
        
        if (!file_exists($this->getAbsolutePath($path))) {
            mkdir($this->getAbsolutePath($path), 0777, TRUE);
        }

        $absoluteFilename = $this->getAbsolutePath($path . $filename);
        
        return file_put_contents($absoluteFilename, $content);
    }
}
