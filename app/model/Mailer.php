<?php

namespace Model;

use DateTime;
use PHPMailer;
use Nette\Utils\Html;

class Mailer extends \Nette\Object 
{	
	public function sendPasswordResetEmail($presenter, $email, $token) {
        $mail = new PHPMailer();
        $mail->Subject = 'Password reset';
        $mail->IsHTML(TRUE);
        $mail->CharSet = "utf-8";
        $mail->From = 'honza.martinek@gmail.com';
        $mail->FromName = 'Peer Blender';
        $mail->AddAddress($email); //

        $header = Html::el('h1')->setText('Peer Blender password reset');
        
        $link = $presenter->link('//Password:new', array('email' => $email, 'token' => $token));
        $text = '<p>You can reset your password on Peer Blender by clicking on this link:<br>'
        	. '<a href="' . $link . '">' . $link . '</a>.</p>';
        
        $mail->MsgHTML($header . $text);
        $mail->Send();
    }	
}