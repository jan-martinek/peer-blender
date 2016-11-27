<?php

namespace App\Presenters;

/**
 * Homepage presenter.
 */
class CodePreviewPresenter extends BasePresenter
{
    /** @var \Model\Repository\CourseRepository @inject */
    public $courseRepository;
    
    /** @var \Model\Repository\AnswerRepository @inject */
    public $answerRepository;

    public function renderTurtle($id, $animated = TRUE)
    {
    	$answer = $this->answerRepository->find($id);
    	$this->template->code = $answer->text;
    	$this->template->animated = $animated;
    	
    }
    
    public function actionHidden()
    {
        $post = $this->request->getPost();
        $this->template->answer = isset($post['answer']) ? $post['answer'] : '';
    }
}
