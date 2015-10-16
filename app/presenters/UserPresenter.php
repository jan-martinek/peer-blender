<?php

namespace App\Presenters;

class UserPresenter extends BasePresenter
{
    /** @var \Model\Repository\AssignmentRepository @inject */
    public $assignmentRepository;
    
    /** @var \Model\Repository\ReviewRepository @inject */
    public $reviewRepository;
    
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

}
