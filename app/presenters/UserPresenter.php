<?php

namespace App\Presenters;

class UserPresenter extends BasePresenter
{
	
	public function actionDefault($id) 
    {
        $this->userEntity->setFavoriteRepository($this->favoriteRepository);
	}
	
    public function renderDefault($id)
    {
        $this->template->userEntity = $this->userEntity;
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
