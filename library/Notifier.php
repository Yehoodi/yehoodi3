<?php

class Notifier
{
	protected $user;
	protected $identity;
		
	public function __construct($db, $userId = array())
	{
		$this->db = $db;

		if(!is_array($userId) && !count($userId)) {
			return false;
		}

		$this->users = $userId;
		// get current user's identity
		$auth = Zend_Auth::getInstance();
    	if ($auth->hasIdentity()) {
    		$this->identity = $auth->getIdentity();
    	}
	}
	
	/**
	 * Sends a message to the user using
	 * the template provided in the 
	 * paramater
	 *
	 * @param object $user
	 * @param template $tpl
	 */
	public function sendNotification($tpl, $rsrcId = null, $debug = 0)
	{
		//$messageType = $this->checkMessageSettings();
		
	    $templater = new Templater();
	    
	    if (!is_array($this->users)) {
	    	return false;
	    }

	    if(!$rsrcId) {
			return false;
		}
	    
	    // Since $this->user is now an array of user ids we need to
	    // iterate through them, get user objects for each one
	    // and then email them out.
	    
	    $user = new DatabaseObject_User($this->db);

	    foreach ($this->users as $value) {
	    	$userId = $value['user_id'];

	    	// don't send to the current user
	    	if ($userId == $this->identity->user_id) {
	    		continue;
	    	}
	    	
	    	$user->load($userId);
	    	
	    	$templater->user = $user;
	    	$options = array('rsrc_id' => $rsrcId);
	    	
	    	// get the title
	    	$titleArray = DatabaseObject_Resource::getResourceTitleByResourceId($this->db, $options);
	    	
	    	// build the link to the comment
	    	$totalComments = DatabaseObject_Comment::GetCommentCount($this->db, $options);
	    	$limit = Zend_Registry::get('paginationConfig')->CommentsPerPage;
			$lastPage = ceil($totalComments / $limit);
			$lastComment = $totalComments;
	    	$url = DatabaseObject_Resource::getResourceUrl($this->db, $rsrcId);
	    	$link = Zend_Registry::get('serverConfig')->location . 'comment/' . $rsrcId . '/' . $url . '/' . $lastPage . '#comment_'.$lastComment;
	    	$siteUrl = Zend_Registry::get('serverConfig')->location;
	    	
	    	// assign to template
	    	$templater->resourceTitle = $titleArray['title'];
	    	$templater->link = $link;
	    	$templater->siteURL = $siteUrl;
		
		    // fetch the e-mail body
		    $body = $templater->render('email/' . $tpl);
		
		    // extract the subject from the first line
		    list($subject, $body) = preg_split('/\r|\n/', $body, 2);
		
		    if (!$debug) {
			    
			    // now set up and send the e-mail
			    $mail = new Zend_Mail();
			
			    // set the to address and the user's full name in the 'to' line
			    $mail->addTo($user->email_address,
			                 trim($user->user_name));
			
			    // get the 'from' details from the config
			    $mail->setFrom(Zend_Registry::get('emailConfig')->fromEmail, Zend_Registry::get('emailConfig')->fromName);
			
			    // set the subject and body and send the mail
			    $mail->setSubject(trim($subject));
			    $mail->setBodyText(trim($body));
			    try {
			    	$mail->send();
			    } catch (Exception $e) {
			    	// echo "didn't send";die;
			    	// log the failure
			    	
			    }
			    
			    // Update the table with NOTIFIED_YES
			    $options = array('user_id' => $userId,
			    				 'rsrc_id' => $rsrcId
			    				 );
			    				 
			    $notify = new DatabaseObject_UserResourceNotify($this->db);
			    $notifyId = DatabaseObject_UserResourceNotify::getNotify($this->db, $options);
			    $notify->load($notifyId);
			    $notify->notify_status = DatabaseObject_UserResourceNotify::NOTIFIED_YES ;
			    $notify->save();
			    
		    } else {
		    	// log this email instead of sending it
/*		    	$writer = new Zend_Log_Writer_Stream(Zend_Registry::get('emailConfig')->debugLog);
				$formatter = new Zend_Log_Formatter_Simple('EMAIL: %message%' . PHP_EOL);
				$writer->setFormatter($formatter);
				
				$logger = new Zend_Log();
				$logger->addWriter($writer);
				
				$logger->log('So this turns out to be nothing' . (string)$body . 'so what gives?', Zend_Log::INFO );
*/
				Zend_Debug::dump($body);die;
		    }
	    }
	}
	
	
	/**
	 * Sends a message to the user 
	 * notifying about a new Yehoodi 
	 * mail message
	 *
	 * @param object $user
	 * @param template $tpl
	 */
	public function sendMailNotification($tpl, $mailId = null, $debug = 0)
	{
	    $templater = new Templater();
	    
	    if (!is_array($this->users)) {
	    	return false;
	    }

	    if(!$mailId) {
			return false;
		}
	    
	    $user = new DatabaseObject_User($this->db);

	    foreach ($this->users as $value) {
	    	$userId = $value['user_id'];
	    	$user->load($userId);
	    	
	    	$options = array('mail_id' => $mailId);
	    	
	    	// get the threadId
	    	$threadId = DatabaseObject_Mail::getThreadId($this->db, $options);

	    	// get the from user
	    	$recipientId = DatabaseObject_Mail::getUserIdOfThreadRecipient($this->db, $threadId);
			$recipient = new DatabaseObject_User($this->db);
			$recipient->load($recipientId);
			//Zend_Debug::dump($recipient);die;
	    	
	    	// check if the recipient wants to be notified by email
	    	if (!$recipient->profile->notify_by_email) {
	    		return;
	    	}
			
			$recipientUserName = $recipient->user_name;
			$recipientFirstName = $recipient->profile->first_name;
			$recipientEmail = $recipient->email_address;

	    	// get the subject
	    	$subject = DatabaseObject_Mail::getSubjectById($this->db, $options);
	    	
	    	// get the email contents
	    	$textArray = DatabaseObject_MailBody::getMailBodyIdByMailId($this->db, $options);
	    	$text = $textArray['mail_body'];
	    	
	    	// Generate bbcode links for any content that contains links and such
        	$text = nl2br($text);
        	$text = strip_tags($text);
	    	
	    	// build the link to the mail
	    	$link = Zend_Registry::get('serverConfig')->location . 'mail/message/' . $threadId . '#message_' . $mailId;

	    	//$link = Zend_Registry::get('serverConfig')->location . 'comment/' . $rsrcId . '/' . $url . '/' . $lastPage . '#comment_'.$lastComment;
	    	
	    	// assign to template
	    	$templater->user = $user;
	    	$templater->subject = $subject;
	    	$templater->text = $text;
	    	$templater->recipientUserName = $recipientUserName;
	    	$templater->recipientFirstName = $recipientFirstName;
	    	$templater->link = $link;
		
		    // fetch the e-mail body
		    $body = $templater->render('email/' . $tpl);
		
		    // extract the subject from the first line
		    list($subject, $body) = preg_split('/\r|\n/', $body, 2);
		
		    if (!$debug) {
			    
			    // now set up and send the e-mail
			    $mail = new Zend_Mail();
			
			    // set the to address and the user's full name in the 'to' line
			    $mail->addTo($recipientEmail,
			                 trim($recipientUserName));
			
			    // get the 'from' details from the config
			    $mail->setFrom(Zend_Registry::get('emailConfig')->fromEmail, Zend_Registry::get('emailConfig')->fromName);
			
			    // set the subject and body and send the mail
			    $mail->setSubject(trim($subject));
			    $mail->setBodyText(trim($body));
			    try {
			    	$mail->send();
			    } catch (Exception $e) {
			    	// echo "didn't send";die;
			    	// log the failure
			    	
			    }
			    
		    } else {
		    	// log this email instead of sending it
/*		    	$writer = new Zend_Log_Writer_Stream(Zend_Registry::get('emailConfig')->debugLog);
				$formatter = new Zend_Log_Formatter_Simple('EMAIL: %message%' . PHP_EOL);
				$writer->setFormatter($formatter);
				
				$logger = new Zend_Log();
				$logger->addWriter($writer);
				
				$logger->log('So this turns out to be nothing' . (string)$body . 'so what gives?', Zend_Log::INFO );
*/
				Zend_Debug::dump($body);die;
		    }
	    }
	}

	/**
	 * Sends out an email to the mods
	 * from a user on the site 
	 *
	 * @param object $user
	 * @param template $tpl
	 */
	public function sendSpamReportNotification($tpl, $rsrcId = null, $debug = 0)
	{
	    $templater = new Templater();
	    
	    if (!is_array($this->users)) {
	    	return false;
	    }

	    if(!$rsrcId) {
			return false;
		}
	    
//    	$moderators = array(array('user_id' => 1762),
//    						array('user_id' => 4823)
//    				  );
    				  
    	$moderators = DatabaseObject_User::getAllModerators($this->db);

    	//Zend_Debug::dump($moderators);die;
    	
    	$user = new DatabaseObject_User($this->db);
    	$user->load($this->users['user_id']);

    	$mod = new DatabaseObject_User($this->db);
    	
	    // Loop through all the moderators
    	foreach ($moderators as $value) {
	    	$modId = $value['user_id'];
	    	$mod->load($modId);
	    		
	    	$templater->user = $user;
	    	$options = array('rsrc_id' => $rsrcId);
	    	
	    	// get the title
	    	$titleArray = DatabaseObject_Resource::getResourceTitleByResourceId($this->db, $options);
	    	
	    	// build the link to the resource
	    	$url = DatabaseObject_Resource::getResourceUrl($this->db, $rsrcId);
	    	$link = Zend_Registry::get('serverConfig')->location . 'comment/' . $rsrcId . '/' . $url;
	    	$siteUrl = Zend_Registry::get('serverConfig')->location;
	    	
	    	// assign to template
	    	$templater->resourceTitle = $titleArray['title'];
	    	$templater->link = $link;
	    	$templater->siteURL = $siteUrl;
		    // fetch the e-mail body
		    $body = $templater->render('email/' . $tpl);
		
		    // extract the subject from the first line
		    list($subject, $body) = preg_split('/\r|\n/', $body, 2);
		
		    if (!$debug) {
			    
			    // now set up and send the e-mail
			    $mail = new Zend_Mail();
			
			    // set the to address and the user's full name in the 'to' line
			    $mail->addTo($mod->email_address,
			                 trim($mod->user_name));
			
			    // get the 'from' details from the config
			    $mail->setFrom(Zend_Registry::get('emailConfig')->fromEmail, Zend_Registry::get('emailConfig')->fromName);
			
			    // set the subject and body and send the mail
			    $mail->setSubject(trim($subject));
			    $mail->setBodyText(trim($body));
			    try {
			    	$mail->send();
			    	
			    	// Update the table to sent status
					$this->db->query("UPDATE resource_report SET report_status = 1 WHERE rsrc_id = ?", $rsrcId);
			    } catch (Exception $e) {
			    	// echo "didn't send";die;
			    	// log the failure
			    	
			    }
			    
		    } else {
		    	// log this email instead of sending it
/*		    	$writer = new Zend_Log_Writer_Stream(Zend_Registry::get('emailConfig')->debugLog);
				$formatter = new Zend_Log_Formatter_Simple('EMAIL: %message%' . PHP_EOL);
				$writer->setFormatter($formatter);
				
				$logger = new Zend_Log();
				$logger->addWriter($writer);
				
				$logger->log('So this turns out to be nothing' . (string)$body . 'so what gives?', Zend_Log::INFO );
*/
				Zend_Debug::dump($body);die;
		    }
	    }
	}
}