<?php

namespace App\Presenters;

class UserPresenter extends BasePresenter
{
    /** @var \Model\Repository\UserRepository @inject */
    public $userRepository;

    public function renderDefault()
    {
        $this->template->me = $this->userRepository->find($this->user->getId());
    }

}
