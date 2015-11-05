<?php

namespace Model;

use Nette;
use Nette\Utils\Strings;

class UploadStorage extends Storage
{
    public function moveUploadedFile($file, $path)
    {   
        $filename = Strings::webalize(pathinfo($file->name, PATHINFO_FILENAME))
            . '.' . pathinfo($file->name, PATHINFO_EXTENSION);
        
        if (!file_exists($this->getAbsolutePath($path))) {
            mkdir($this->getAbsolutePath($path), 0777, TRUE);
        }

        $absoluteFilename = $this->getAbsolutePath($path . $filename);
        
        $file->move($absoluteFilename);
        
        return $path . $filename;
    }
}
