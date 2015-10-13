<?php

namespace App\Presenters;

class UserPresenter extends BasePresenter
{
    /** @var \Model\Repository\AssignmentRepository @inject */
    public $assignmentRepository;
    
    /** @var \Model\Repository\ReviewRepository @inject */
    public $reviewRepository;
	
	public function actionDefault($id) 
    {
        $this->userEntity->setFavoriteRepository($this->favoriteRepository);
	}
	
    public function renderDefault($id)
    {
        $this->template->userEntity = $this->userEntity;
        $this->template->assignmentRepository = $this->assignmentRepository;
        $this->template->reviewRepository = $this->reviewRepository;
        $this->template->isFavorited = $this->userEntity->isFavoritedBy($this->userEntity);
    }

	public function handleFavorite() 
    {
        $this->userEntity->favorite($this->userRepository->find($this->user->id));
        $this->redirect('this');
    }

    public function renderMe()
    {
    	$user = $this->userRepository->find($this->user->id);
    	$this->redirect('User:default', $user->id);
    }

}
