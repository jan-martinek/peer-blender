<?php

namespace App\Presenters;

use Nette\Application\Responses\JsonResponse;
use Michelf\Markdown;
use Model\Entity\Course;
use Model\GeneratedFilesStorage;

/**
 * Chat presenter.
 */
class ChatPresenter extends BasePresenter
{   
	/** @var \Model\Repository\CourseRepository @inject */
    public $courseRepository;
	
    /** @var \Model\Repository\MessageRepository @inject */
    public $messageRepository;
    
    /** @var GeneratedFilesStorage @inject */
    public $generatedFilesStorage;
  

    public function renderJson($id, $toFile = FALSE)
    {
    	$messages = $this->getMessages($this->courseRepository->find($id));

    	if ($toFile) {
        	$this->generatedFilesStorage->save(json_encode($messages), $id, 'json', '/chat/');
        	$this->terminate();
        } else {
        	$this->sendResponse(new JsonResponse($messages));
        }
    }
    
    public function renderHtml($id, $toFile = FALSE)
    {
    	$messages = $this->messageRepository->findLatest($this->courseRepository->find($id));
    	
        $content = '';
        foreach ($messages as $message) {
        	$content .=  '<div class="message">'
                . Markdown::defaultTransform('[**' . $message->user->name . '**](' 
                    . $this->link('User:default', $message->user->id) 
                    . ') ' 
                    . $message->text)
                . '</div>';
        }
        
        if ($toFile) {
        	$this->generatedFilesStorage->save($content, $id, 'html', '/chat/');
        } else {
        	echo $content;
        }
        
        $this->terminate();
    }
    
    public function getMessages(Course $course)
    {
    	$messages = $this->messageRepository->findLatest($course);
        
        $chatlog = array();
        foreach ($messages as $message) {
        	$chatlog[] = array(
        		'user_id' => $message->user->id,
        		'user_name' => $message->user->name,
        		'unit_id' => $message->unit->id,
        		'unit_name' => $message->unit->name,
        		'time' => $message->submitted_at->format('j. n. Y H.i'),
        		'text' => $message->text
        	);
        }
        
        return $chatlog;
    }

}
