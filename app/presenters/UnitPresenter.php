<?php

namespace App\Presenters;

use App\Components\HomeworkForm;
use DateTime;
use Model\Entity\Log;
use Model\Entity\Solution;
use Model\Entity\Answer;
use Nette\Utils\Strings;

/**
 * Unit presenter.
 */
class UnitPresenter extends BasePresenter
{
    /** @var \Model\Repository\CourseRepository @inject */
    public $courseRepository;
    
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
    
    /** @var \Model\Repository\QuestionRepository @inject */
    public $questionRepository;
    
    /** @var \Model\UploadStorage @inject */
    public $uploadStorage;
    
    /** @var array */
    public $questions;
    
    public function actionDefault($id) 
    {   
        $unit = $this->courseInfo->init($this->unitRepository->find($id));
        if (!$unit->hasBeenPublished()) {
            throw new \Nette\Application\BadRequestException(NULL, 404);
            return;
        }
        
        $unit->setFavoriteRepository($this->favoriteRepository);
        $this->template->isFavorited = $unit->isFavoritedBy($this->userInfo);
        
        $assignment = $this->assignmentRepository->getMyAssignment($this->courseInfo->unit, $this->userInfo);
        $this->questions = $assignment->questions;
        $this->courseInfo->setSolution($assignment->solution);
        
        $this->logEvent($unit, 'open');
    }

    public function renderDefault($id)
    {
        $this->template->unit = $this->courseInfo->unit;
        $this->template->assignment = $this->courseInfo->assignment;
        $this->template->course = $this->courseInfo->course;
        $this->template->solution = $this->courseInfo->solution;
        
        if ($this->courseInfo->solution) {
            $this->template->answers = $this->courseInfo->solution->answers;
        }
        
        $this->template->uploadPath = $this->uploadStorage->path;
        $this->template->reviews = $this->reviewRepository->findByUnitAndReviewer($this->courseInfo->unit, $this->userInfo);
    }
    
    public function actionTest($id) 
    {
        $unit = $this->courseInfo->init($this->unitRepository->find($id));
        $assignment = $this->courseInfo->setAssignment($this->assignmentRepository->getMyAssignment($unit, $this->userInfo, TRUE));
        $this->questions = $this->courseInfo->assignment->questions;
    }    
    
    public function renderTest($id) 
    {
        $this->template->unit = $this->courseInfo->unit; 
        $this->template->assignment = $this->courseInfo->assignment;
        $this->template->course = $this->courseInfo->course;
    }
    
    public function handleFavorite() 
    {
        $this->courseInfo->unit->favorite($this->userRepository->find($this->user->id));
        $this->redirect('this');
    }
    
    protected function createComponentHomeworkForm() 
    {
        if (is_null($this->questions)) {
            throw new \Nette\Application\BadRequestException;
        }
        
        $form = new HomeworkForm(
            $this->courseInfo->course, 
            $this->courseInfo->assignment->questions,
            $this->translator
        );
        $form->onSuccess[] = array($this, 'homeworkFormSucceeded');
        return $form;
    }
    
    public function homeworkFormSucceeded(HomeworkForm $form, $values) 
    {
        if ($solution = $this->courseInfo->assignment->solution) {
            $solution->edited_at = new DateTime;
            $this->saveAnswers($this->courseInfo->assignment->questions, $values->questions);
            if ($values->attachment->isOK()) 
            {
                $this->removeHomeworkFile($solution->attachment);
                
                $solution->attachment = $this->saveHomeworkFile(
                    $values->attachment, 
                    $this->courseInfo->course->id,
                    $this->courseInfo->unit->id,
                    $this->user->id
                );    
            }
            $this->solutionRepository->persist($solution);
            $this->logEvent($solution, 'edit');
        } else {
            $solution = new Solution;
            $solution->unit = $this->courseInfo->unit;
            $solution->assignment = $this->courseInfo->assignment;
            $solution->user = $this->userInfo;
            $solution->submitted_at = new DateTime;
            $solution->edited_at = new DateTime;
            $this->saveAnswers($this->courseInfo->assignment->questions, $values->questions);
            if ($values->attachment->isOK()) 
            {
                $solution->attachment = $this->saveHomeworkFile(
                    $values->attachment, 
                    $this->courseInfo->course->id,
                    $this->courseInfo->unit->id,
                    $this->user->id
                ); 
            }
            $this->solutionRepository->persist($solution);            
            $this->logEvent($solution, 'create');
        }

        $this->redirect('this');
    }
    
    public function saveAnswers($questions, $answers) 
    {
        foreach ($questions as $order => $question) {
            if (isset($question->answer)) {
                $answer = $question->answer; 
                $answer->text = $answers[$order];
            } else {
                $answer = new Answer;
                $answer->text = $answers[$order];
                $answer->solution = $question->assignment->solution;
                $answer->question = $question;
            }
            $this->answerRepository->persist($answer);
        }
    }
    
    /**
     * @return string uploaded filename
     */
    private function saveHomeworkFile($file, $courseId, $unitId, $userId) 
    {
        if ($file->isOK()) {
            $path = "/course-$courseId/homeworks/unit-$unitId/user-$userId/";
            $filename = 
                Strings::webalize(pathinfo($file->name, PATHINFO_FILENAME))
                . '.' . pathinfo($file->name, PATHINFO_EXTENSION);
            
            if (!file_exists($this->uploadStorage->getAbsolutePath($path))) {
                mkdir($this->uploadStorage->getAbsolutePath($path), 0777, TRUE);
            }

            $absoluteFilename = $this->uploadStorage->getAbsolutePath($path . $filename);
            
            $file->move($absoluteFilename);
            
            return $path . $filename;
        } else {
            return NULL;
        }
    }
    
    private function removeHomeworkFile($filename) 
    {
        $absoluteFilename = $this->uploadStorage->getAbsolutePath($filename);
        
        if (file_exists($absoluteFilename) && is_file($absoluteFilename)) {
            return unlink($absoluteFilename);    
        } 
    }
}
