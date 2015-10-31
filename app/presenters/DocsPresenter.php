<?php

namespace App\Presenters;

use Nette\Application\BadRequestException;

/**
 * Error log presenter
 */
class DocsPresenter extends BasePresenter
{
	private $docsPath = './../docs/';

    public function renderDefault($doc = null)
    {	
        if (is_null($doc)) {
            $docFilename = './../README.md';
        } else {
            $docFilename = $this->docsPath . $doc; 
        }

        if (!file_exists($docFilename)) {
            throw new BadRequestException(404);
        }

        $doc = file_get_contents($docFilename);
        preg_match('@# (.+)@', $doc, $headings);
        $doc = str_replace('./../illustrations', '/images/illustrations', $doc);
        
        $this->template->doc = $doc;
        $this->template->title = isset($headings[1]) ? $headings[1] : '— Unnamed document —';
        
    }
}
