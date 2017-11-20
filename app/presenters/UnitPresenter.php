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




    // temp - move to model
    public function actionRecalcScores($id) {
        if ($this->user->isAllowed('unit', 'batchOps')) {
            $unit = $this->unitRepository->find($id);
            foreach ($unit->assignments as $assignment) {
                if ($assignment->solution) {
                    foreach ($assignment->solution->reviews as $review) {
                        $review->score = $this->calcTotalScore($assignment, $review->getAssessmentSet(), $review->solutionIsComplete);
                        $this->reviewRepository->persist($review);
                        echo $review->id . ' score is now ' . $review->score . '<br>';
                    }
                }
            }
        }
        exit;
    }

    private function calcTotalScore($assignment, $assessments, $solutionIsComplete)
    {
        // if there's a null in the assessments, ratings are incomplete
        $ratingComplete = !in_array(NULL, (array) $assessments, TRUE);
        if (!$ratingComplete) {
            return NULL;
        }
        
        // process assessments
        $scores = array();
        $assignment = $this->produce($assignment);
        
        $rubrics = $assignment->getAllRubrics();
        
        foreach ($rubrics as $i => $rubric) {
            if ($rubric instanceof \Model\Ontology\IRubric) {
                $rubric->setRaw($assessments[$i]);
                $scores[$i] = $rubric->calcScore();
            } elseif (is_int($assessments[$i]) || is_float($assessments[$i])) {
                $scores[$i] = $assessments[$i];
            }
        }
        
        $score = array_sum($scores) / count($scores);
        
        if (!$solutionIsComplete) {
            $score = $score / 2;    
        }
        
        return $score;
    }
    
    
}
