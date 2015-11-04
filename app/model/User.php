<?php

namespace Model\Entity;

use DateTime;
use Model\Mailer;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|NULL $passwordResetToken (password_reset_token)
 * @property DateTime|NULL $passwordResetValidUntil (password_reset_valid_until)
 * @property Enrollment[] $enrollments m:belongsToMany
 * @property Assignment[] $assignments m:belongsToMany
 * @property Review[] $reviews m:belongsToMany(reviewed_by_id:review)
 * @property Solution[] $solutions m:belongsToMany
 */
class User extends FavoritableEntity
{
    private $passwordResetTimespan = '+ 90 minute';
    
    public function initiatePasswordReset() 
    {
        $this->passwordResetToken = substr(md5(rand()), 0, 10);
        $this->passwordResetValidUntil = new DateTime($this->passwordResetTimespan);
    }
    
    public function hasPasswordResetBeenInitiated() 
    {
        return ($this->passwordResetValidUntil >= new DateTime) ? true : false;
    }
    
    public function isPasswordResetTokenValid($token) 
    {
        return ($this->passwordResetToken === $token) ? true : false;
    }
    
    public function sendPasswordResetEmail($presenter) 
    {
        $mailer = new Mailer;
        $mailer->sendPasswordResetEmail($presenter, $this->email, $this->passwordResetToken);
    }
}
