<?php

namespace App\Presenters;

/**
 * Error log presenter
 */
class ErrorLogPresenter extends BasePresenter
{
	private $logPath = './../log/';

    public function startup() 
    {
        parent::startup();
        
        if (!$this->user->isInRole('admin')) {
            throw new \Nette\Application\BadRequestException('Forbidden', 403);
        }
    }

    public function renderDefault()
    {	
        $this->template->files = glob($this->logPath . '*');
    }
    
    public function actionException($filename) {
    	$file = $this->logPath . $filename;
    	
    	if (file_exists($file)) {
    		echo file_get_contents($file);	
    	} else {
    		echo 404;
    	}
    	$this->terminate();
    }
}
