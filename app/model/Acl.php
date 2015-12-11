<?php

namespace Model;

use Nette\Security\Permission;

class Acl extends Permission 
{

	public function __construct() 
	{
		$this->addRole('guest');
		$this->addRole('registered', 'guest');
		$this->addRole('admin', 'registered');
		
		$this->addRole('course-student');
		$this->addRole('course-assistant');
		$this->addRole('course-admin', 'course-assistant');

		$this->addResource('assignment');
		$this->addResource('review');
		$this->addResource('reviewComment');
		$this->addResource('course');
		$this->addResource('solution');
		$this->addResource('unit');
		$this->addResource('errors');
		
		$this->allow('course-assistant', 'course', 'viewStats');
		$this->allow('course-assistant', 'solution', 'viewAnytime');
		$this->allow('course-assistant', 'solution', 'viewLog');
		$this->allow('course-assistant', 'review', array(
			'writeAnytime',
			'commentAnytime',
			'evaluateFix',
			'evaluateObjection',
			'announceProblem',
			'unlock'
		));
		
		$this->allow('admin', 'errors', 'view');
	}

}
