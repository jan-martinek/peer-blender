<?php

namespace App\Components;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Model\Repository\MessageRepository;
use Model\GeneratedFilesStorage;
use Model\Entity\Message;
use DateTime;


class ChatControl extends Control
{
    private $messageRepository;
    
    private $generatedFilesStorage;
    
    private $userRepository;
    
    public function __construct(MessageRepository $messageRepository, GeneratedFilesStorage $generatedFilesStorage) 
    {
        $this->messageRepository = $messageRepository;
        $this->generatedFilesStorage = $generatedFilesStorage;
    }
    
    public function setTemplateFilters() 
    {
        //markdown
        $this->template->addFilter('md', function ($s) {
            return \Michelf\Markdown::defaultTransform($s);
        });
        //inline markdown
        $this->template->addFilter('imd', function ($s) {
            return strip_tags(Markdown::defaultTransform($s), '<a><strong><em>');
        });
    }
    
    public function render()
    {
        $template = $this->template;
        $this->setTemplateFilters();
        $template->setFile(__DIR__ . '/chat.latte');
        $template->messages = $this->messageRepository->findLatest($this->presenter->courseInfo->course);
        $template->render();
    }
    
    protected function createComponentMessageForm() 
    {
        $form = new Form;
        $form->addTextarea('message');
        $form->addSubmit('submit');
        $form->onSuccess[] = array($this, 'messageFormSucceeded');
        return $form;
    }
    
    public function messageFormSucceeded(Form $form, $values) 
    {   
        if (trim($values->message) === '') {
            return;
        }
        
        $message = new Message;
        $message->text = $values->message;
        $message->course = $this->presenter->courseInfo->course;
        $message->unit = $this->presenter->courseInfo->unit;
        $message->user = $this->presenter->userInfo;
        $message->submitted_at = new DateTime;        
        $this->messageRepository->persist($message);
        $this->generatedFilesStorage->remove($this->presenter->courseInfo->course->id, 'html', '/chat/');
        
        $form->setValues(array(), TRUE);
        $this->redrawControl('chatForm');
        $this->redrawControl('messages');
    }      
    
    public function handleRefreshChat() 
    {
        if ($this->presenter->isAjax()) {
            $this->invalidateControl('messages');
            $this->redrawControl('messages');
        }
    }
}
