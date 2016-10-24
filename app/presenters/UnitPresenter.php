<?php

namespace App\Presenters;

use App\Components\AssignmentForm;
use DateTime;
use Model\Entity\Log;

/**
 * Unit presenter.
 */
class UnitPresenter extends BasePresenter
{
    /** @var \Model\Repository\UnitRepository @inject */
    public $unitRepository;
    
    /** @var \Model\Repository\AssignmentRepository @inject */
    public $assignmentRepository;

    /** @var \Model\Repository\ReviewRepository @inject */
    public $reviewRepository;
    
    /** @var \Model\Repository\SolutionRepository @inject */
    public $solutionRepository;
    
    /** @var \Model\Repository\AnswerRepository @inject */
    public $answerRepository;
    
    /** @var \Model\UploadStorage @inject */
    public $uploadStorage;
    
    public function actionDefault($id, $lateEdits = FALSE) 
    {   
        $info = $this->courseRegistry;
        
        $unit = $this->unitRepository->find($id);
        $info->insert($unit);
        $product = $this->produce($unit);
        
        if (!$product->hasBeenPublished()) {
            throw new \Nette\Application\BadRequestException(NULL, 404);
            return;
        }
        
        $assignment = $this->assignmentRepository->findByUnitAndUser($unit, $this->userInfo);
        
        
        if (!$assignment) {
            $assignment = $this->courseFactory->assembleAssignment($unit);
            $assignment->student = $this->userInfo;
            $this->assignmentRepository->persist($assignment);
        }
        $this->template->assignment = $this->courseFactory->produceAssignment($assignment);
        $info->insert($assignment);
        
        if (isset($assignment->solution)) {
            $info->setSolution($assignment->solution);
            $this->template->solution = $assignment->solution;
        } else {
            $this->template->solution = null;
        }
        
        $this->template->lateEdits = $lateEdits;
        
        $this->template->isFavorited = $unit->isFavoritedBy($this->userInfo);
        $this->logEvent($unit, 'open');
        
        $this['questionsRenderer']->assignment = $this->produce($assignment);
        $this['questionsRenderer']->solution = $this->courseRegistry->solution;
        $this['questionsRenderer']->form = $this['assignmentForm'];
        $this['questionsRenderer']->lateEdits = $lateEdits;
    }

    public function renderDefault($id)
    {
        $info = $this->courseRegistry;
        
        $this->deliver($info->unit);
        $this->deliver($info->assignment);
        $this->deliver($info->course);
        
        if ($info->solution) {
            $this->template->solution = $info->solution;
        }
        
        $this->template->uploadPath = $this->uploadStorage->path;
        $this->template->reviews = $this->reviewRepository->findByUnitAndReviewer($info->unit, $this->userInfo);
    }
    
    /*
     * One-time fix (transition from geomean to 1/2 multiplier)
     */
    public function actionRecalcReviews($id) 
    {
        if ($this->user->isAllowed('solution', 'viewAnytime')) {
            $info = $this->courseRegistry;
            $unit = $this->unitRepository->find($id);
            
            $reviews = $this->reviewRepository->findByUnit($unit);
            foreach ($reviews as $review) {
                $newScore = $review->calcScore();
                if ($newScore !== FALSE) {
                    echo $review->solutionIsComplete . ' ' . $review->id . ' ' . $review->score . '-->' . $newScore . '<br>';
                    
                    $review->score = $newScore;
                    $this->reviewRepository->persist($review);
                } else {
                    echo 'NOPE ' . $review->solutionIsComplete . ' ' . $review->id . ' ' . $review->score . '-->' . $newScore . '<br>';
                }
            }
        }
        die;
    }
    
    public function handleFavorite() 
    {
        $this->courseRegistry->unit->favorite($this->userRepository->find($this->user->id));
        $this->redirect('this');
    }
    
    protected function createComponentAssignmentForm() 
    {
        if (!$this->courseRegistry->assignment) {
            throw new \Nette\Application\BadRequestException;
        }
        
        $form = new AssignmentForm(
            $this, $this->produce($this->courseRegistry->assignment->questions)
        );
        
        return $form;
    }
    
    
}
