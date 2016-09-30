<?php

namespace App\Presenters;

use Nette\Application\UI\Form;
use Nette\Application\BadRequestException;

class UserPresenter extends BasePresenter
{
    /** @var \Model\Repository\AssignmentRepository @inject */
    public $assignmentRepository;
    
    /** @var \Model\Repository\ReviewRepository @inject */
    public $reviewRepository;
    
    /** @var \Model\Repository\EnrollmentRepository @inject */
    public $enrollmentRepository;
    
    private $enrollment;
    private $userProfile;
    
	public function actionDefault($id) 
    {
        $this->userProfile = $this->userRepository->find($id);
        $this->userProfile->setFavoriteRepository($this->favoriteRepository);
        $this->template->isFavorited = $this->userProfile->isFavoritedBy($this->userInfo); 
	}
	
    public function renderDefault($id)
    {
        $this->template->userProfile = $this->userProfile;   
        $this->template->assignmentRepository = $this->assignmentRepository;
        $this->template->reviewRepository = $this->reviewRepository;
    }
    
    public function actionNotes($id)
    {
        $this->enrollment = $this->enrollmentRepository->find($id);
    }
    
    public function renderNotes($id)
    {
        if (!$this->user->isAllowed('user', 'editNotes')) {
            throw new BadRequestException('You cannot edit user\'s notes.', 403);
        }
        
        $this->template->enrollment = $this->enrollment;
        $this->template->userProfile = $this->template->enrollment->user;   
    }

	public function handleFavorite() 
    {
        $this->userProfile->favorite($this->userRepository->find($this->user->id));
        $this->redirect('this');
    }

    public function renderMe()
    {
    	$user = $this->userRepository->find($this->user->id);
    	$this->redirect('User:default', $user->id);
    }


    protected function createComponentNotesForm() 
    {
        $form = new Form;

        $notesLabel = $this->translator->translate('messages.user.notes');
        $form->addTextarea('notes', $notesLabel, $this->enrollment->notes)->setValue($this->enrollment->notes);
        
        $hiddenNotesLabel = $this->translator->translate('messages.user.hiddenNotes');
        $form->addTextarea('hiddenNotes', $hiddenNotesLabel)->setValue($this->enrollment->hiddenNotes);
        
        $submitLabel = $this->translator->translate('messages.user.saveNotes');
        $form->addSubmit('submit', $submitLabel);
        
        $form->onSuccess[] = array($this, 'notesFormSucceeded');
        
        return $form;
    }
    
    private function calcScoreAdjustment($notes) {
        preg_match_all("/\*(-?[0-9\.]+)/", $notes, $scoreAdjustments);
        return array_sum($scoreAdjustments[1]);
    }
    
    public function notesFormSucceeded(Form $form, $values) 
    {
        if (!$this->user->isAllowed('user', 'editNotes')) {
            throw new BadRequestException('You cannot edit user\'s notes.', 403);
        }
        
        $this->enrollment->notes = $values->notes;
        $this->enrollment->hiddenNotes = $values->hiddenNotes;
        $this->enrollment->scoreAdjustment = $this->calcScoreAdjustment($values->notes);
        $this->enrollmentRepository->persist($this->enrollment);
        
        $this->redirect('User:default#enrollment-' . $this->enrollment->id, $this->enrollment->user->id);
    }

}
