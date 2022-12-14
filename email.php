<?php

	if(isset($_GET['test'])){
		error_reporting(E_ALL);
		ini_set('display_errors', '1');
	}


	function sendEmail($fullname,$username,$email,$comments='',$successJson=false){
		$mailto = $username.$email.".university.ac.jp";
		if(isset($_GET['test'])){
			$mailto = 'your_email@mail.com';
		}
		/*
				CHFEEDBACK.PHP Feedback Form PHP Script Ver 2.15.0
				Generated by thesitewizard.com's Feedback Form Wizard 2.15.0.
				Copyright 2000-2009 by Christopher Heng. All rights reserved.
				thesitewizard is a trademark of Christopher Heng.

				Get the latest version, free, from:
						http://www.thesitewizard.com/wizards/feedbackform.shtml

			You can read the Frequently Asked Questions (FAQ) at:
				http://www.thesitewizard.com/wizards/faq.shtml

			I can be contacted at:
				http://www.thesitewizard.com/feedback.php
			Note that I do not normally respond to questions that have
			already been answered in the FAQ, so *please* read the FAQ.

				LICENCE TERMS

				1. You may use this script on your website, with or
				without modifications, free of charge.

				2. You may NOT distribute or republish this script,
				whether modified or not. The script can only be
				distributed by the author, Christopher Heng.

				3. THE SCRIPT AND ITS DOCUMENTATION ARE PROVIDED
				"AS IS", WITHOUT WARRANTY OF ANY KIND, NOT EVEN THE
				IMPLIED WARRANTY OF MERCHANTABILITY OR FITNESS FOR A
				PARTICULAR PURPOSE. YOU AGREE TO BEAR ALL RISKS AND
				LIABILITIES ARISING FROM THE USE OF THE SCRIPT,
				ITS DOCUMENTATION AND THE INFORMATION PROVIDED BY THE
				SCRIPTS AND THE DOCUMENTATION.

				If you cannot agree to any of the above conditions, you
				may not use the script.

				Although it is not required, I would be most grateful
				if you could also link to thesitewizard.com at:

					 http://www.thesitewizard.com/

		*/

		// ------------- CONFIGURABLE SECTION ------------------------

		// $mailto - set to the email address you want the form
		// sent to, eg
		//$mailto		= "youremailaddress@example.com" ;



		// $subject - set to the Subject line of the email, eg
		//$subject	= "Feedback Form" ;



		// the pages to be displayed, eg
		//$formurl		= "http://www.university.ac.jp/contactleander.htm" ;
		//$errorurl		= "http://www.university.ac.jp/error.htm" ;
		//$thankyouurl	= "http://www.example.com/thankyou.html" ;


		$blank_is_blank = 1;
		$email_is_required = 1;
		$name_is_required = 1;
		$comments_is_required = 1;
		$uself = 0;
		$use_envsender = 0;
		$use_sendmailfrom = 0;
		$use_webmaster_email_for_from = 0;
		$use_utf8 = 1;
		$my_recaptcha_private_key = '' ;

		// -------------------- END OF CONFIGURABLE SECTION ---------------

		$headersep = (!isset( $uself ) || ($uself == 0)) ? "\r\n" : "\n" ;
		$content_type = (!isset( $use_utf8 ) || ($use_utf8 == 0)) ? 'Content-Type: text/plain; charset="iso-8859-1"' : 'Content-Type: text/plain; charset="utf-8"' ;
		if (!isset( $use_envsender )) { $use_envsender = 0 ; }
		if (isset( $use_sendmailfrom ) && $use_sendmailfrom) {
			ini_set( 'sendmail_from', $mailto );
		}
		$envsender = "-f$mailto" ;
		$senderName = "My Courses Online";
		$subject = "Online Confirmation (from Saitama University)";
		$http_referrer = getenv( "HTTP_REFERER");

		$fromemail = 'noreply.your_name@mail.university.ac.jp';


		$comments = stripslashes( $comments );


		$messageproper =
		//	"This message was sent from:\n" .
			//"$http_referrer\n" .
	//		"------------------------------------------------------------\n" .
	//		"Name of sender: $senderName\n" .
	//		"Email of sender: $fromemail\n" .
	//		"------------------------- COMMENTS -------------------------\n\n" .
			$comments .
			"\n\n------------------------------------------------------------\n" ;

		$headers =
			"From: \"$senderName\" <$fromemail>" . $headersep . "Reply-To: \"$fullname\" <$mailto>" . $headersep . "X-Mailer: chfeedback.php 2.15.0" .
			$headersep . 'MIME-Version: 1.0' . $headersep . $content_type ;

		$result = '';
		if ($use_envsender) {
			$result = mail($mailto, $subject, $messageproper, $headers, $envsender );
		}
		else {
			$result = mail($mailto, $subject, $messageproper, $headers );
		}
		return $successJson ? $successJson : ['success'=>'confirmation supposed to be sent','to'=>$mailto,'mail result'=>$result];
	}

	function sendConfirmation($fullname,$username,$email,$code){
		$comments = 'Dear '.$fullname.",\n\n".
								"Thank you for signing up for My Courses Online at Saitama University. Your confirmation code is:\n\n".
								$code."\n\n".
								"(If you did not apply for membership in My Courses Online, please disregard this message.)\n\n".
								"Regards,\n\nLeander Hughes\nSaitama University Center for English Education";
		$successJson = ['success'=>'confirmationSent','to'=>'________'.$email.'.university.ac.jp'];
		return sendEmail($fullname,$username,$email,$comments,$successJson);
	}

	function sendPasswordResetCode($fullname,$username,$email,$code){
		$comments = 'Dear '.$fullname.",\n\n".
								"A request has been made to reset your password. To reset it, please use the following reset code:\n\n".
								$code."\n\n".
								"(If you did not request a password change, please disregard this message.)\n\n".
								"Regards,\n\nLeander Hughes\nSaitama University Center for English Education";
		$successJson = ['success'=>'resetCodeSent','to'=>$username.$email.'.university.ac.jp'];
		return sendEmail($fullname,$username,$email,$comments,$successJson);
	}
