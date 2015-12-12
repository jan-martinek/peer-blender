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
    
    public function actionDefault($id, $lateEdits = FALSE) 
    {   
        $unit = $this->setupCourseInfo($this->unitRepository->find($id));
        if (!$unit->hasBeenPublished()) {
            throw new \Nette\Application\BadRequestException(NULL, 404);
            return;
        }
        
        $assignment = $this->assignmentRepository->getMyAssignment($this->courseInfo->unit, $this->userInfo, $this->questionRepository);
        $this->setupCourseInfo($assignment);
        if (isset($assignment->solution)) {
            $this->courseInfo->setSolution($assignment->solution);
        }
        
        $this->template->lateEdits = $lateEdits;
        
        $this->template->isFavorited = $unit->isFavoritedBy($this->userInfo);
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
        $unit = $this->setupCourseInfo($this->unitRepository->find($id));
        $assignment = $this->courseInfo->setAssignment($this->assignmentRepository->getMyAssignment($unit, $this->userInfo, TRUE));
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
        if (!$this->courseInfo->assignment) {
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
        if ($solution = $this->courseInfo->solution) {
            $solution->edited_at = new DateTime;
            if ($values->attachment->isOK()) 
            {
                $this->removeHomeworkFile($solution->attachment);
                $solution->attachment = $this->saveHomeworkFile(
                    $values->attachment, 
                    $this->courseInfo->course->id,
                    $this->courseInfo->unit->id,
                    $this->user->id
                );    
            } else if ($values->attachmentNotNeeded) {
                $this->removeHomeworkFile($solution->attachment);
                $solution->attachment = $this->saveBlankHomeworkFile(
                    $this->courseInfo->course->id,
                    $this->courseInfo->unit->id,
                    $this->user->id
                );
            }
            $this->solutionRepository->persist($solution);
            $this->saveAnswers($this->courseInfo->assignment->questions, $values->questions);
            $this->logEvent($solution, 'edit');
        } else {
            $solution = new Solution;
            $solution->unit = $this->courseInfo->unit;
            $solution->assignment = $this->courseInfo->assignment;
            $solution->user = $this->userInfo;
            $solution->submitted_at = new DateTime;
            $solution->edited_at = new DateTime;
            if ($values->attachment->isOK()) 
            {
                $solution->attachment = $this->saveHomeworkFile(
                    $values->attachment, 
                    $this->courseInfo->course->id,
                    $this->courseInfo->unit->id,
                    $this->user->id
                ); 
            } else if ($values->attachmentNotNeeded) {
                $solution->attachment = $this->saveBlankHomeworkFile(
                    $this->courseInfo->course->id,
                    $this->courseInfo->unit->id,
                    $this->user->id
                );
            }
            $this->solutionRepository->persist($solution);    
            $this->saveAnswers($this->courseInfo->assignment->questions, $values->questions);        
            $this->logEvent($solution, 'create');
        }
        
        $backToButton = '';
        $httpData = $form->getHttpData();
        foreach (array_keys($httpData) as $k) {
            if (preg_match('/^quick-save-button-[0-9]+$/', $k)) {
                 $backToButton = '#' . $k;
            }
        }
        
        $this->redirect('this' . $backToButton);
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
            return $this->uploadStorage->moveUploadedFile($file, $path);
        } else {
            return NULL;
        }
    }
    
    private function saveBlankHomeworkFile($courseId, $unitId, $userId) 
    {   
        $path = "/course-$courseId/homeworks/unit-$unitId/user-$userId/";
        return $this->uploadStorage->createFile(
            $this->translator->translate('messages.unit.homeworkAttachmentNotNeeded'),
            $path
        );
    }    
    
    private function removeHomeworkFile($filename) 
    {
        $absoluteFilename = $this->uploadStorage->getAbsolutePath($filename);
        
        if (file_exists($absoluteFilename) && is_file($absoluteFilename)) {
            return unlink($absoluteFilename);    
        } 
    }
}
