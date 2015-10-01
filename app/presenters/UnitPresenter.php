<?php

namespace App\Presenters;

use App\Components\HomeworkForm;
use DateTime;
use Model\Entity\Log;
use Model\Entity\Solution;
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
    
    /** @var \Model\UploadStorage @inject */
    public $uploadStorage;
    
    public $questions;    
    public $course;
    
    private $unit;
    private $assignment;
    private $solution;
    
    public function actionDefault($id) 
    {
        $this->unit = $this->unitRepository->find($id);
        $this->course = $this->unit->course;
        
        $this->unit->setFavoriteRepository($this->favoriteRepository);
        $this->template->isFavorited = $this->unit->isFavoritedBy($this->userEntity);
        
        $this->assignment = $this->assignmentRepository->getMyAssignment($this->unit, $this->userEntity);        
        $this->questions = unserialize($this->assignment->questions);
        
        $this->solution = $this->assignment->solution;
        
        $this->logEvent($this->unit, 'open');
    }

    public function renderDefault($id)
    {
        $this->template->unit = $this->unit; 
        $this->template->assignment = $this->assignment;
        $this->template->course = $this->course;
        $this->template->solution = $this->solution;
        
        if ($this->solution) {
            $this->template->answers = unserialize($this->solution->answer);
        }
        
        $this->template->uploadPath = $this->uploadStorage->path;
        
        $this->template->reviews = $this->reviewRepository->findByUnitAndReviewer($this->unit, $this->userEntity);
    }
    
    public function handleFavorite() 
    {
        $this->unit->favorite($this->userRepository->find($this->user->id));
        $this->redirect('this');
    }
    
    protected function createComponentHomeworkForm() 
    {
        if (!$this->questions) {
            throw new Nette\Application\BadRequestException;
        }
        
        $form = new HomeworkForm($this, $this->course);
        $form->onSuccess[] = array($this, 'homeworkFormSucceeded');
        return $form;
    }
    
    public function homeworkFormSucceeded(HomeworkForm $form, $values) 
    {
        if ($solution = $this->assignment->solution) {
            $solution->edited_at = new DateTime;
            $solution->answer = serialize((array) $values->questions);
            if ($values->attachment->isOK()) 
            {
                $this->removeHomeworkFile($solution->attachment);
                
                $solution->attachment = $this->saveHomeworkFile(
                    $values->attachment, 
                    $this->course->id,
                    $this->unit->id,
                    $this->user->id
                );    
            }
            $this->solutionRepository->persist($solution);
            $this->logEvent($solution, 'edit');
        } else {
            $solution = new Solution;
            $solution->unit = $this->unit;
            $solution->assignment = $this->assignment;
            $solution->user = $this->userEntity;
            $solution->submitted_at = new DateTime;
            $solution->edited_at = new DateTime;
            $solution->answer = serialize((array) $values->questions);
            if ($values->attachment->isOK()) 
            {
                $solution->attachment = $this->saveHomeworkFile(
                    $values->attachment, 
                    $this->course->id,
                    $this->unit->id,
                    $this->user->id
                ); 
            }
            $this->solutionRepository->persist($solution);            
            $this->logEvent($solution, 'create');
        }

        $this->redirect('this');
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
        
        if (file_exists($absoluteFilename)) {
            return unlink($absoluteFilename);    
        } 
    }
}
