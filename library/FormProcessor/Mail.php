<?php
    class FormProcessor_Mail extends FormProcessor
    {
		// This is our whitelist of allowed html tags for resources
		// TODO: Get this in a config file somewhere
        static $tags =     '<a>
                            <b>
                            <blockquote>
                            <code>
                            <del>
                            <dd>
                            <dl>
                            <dt>
                            <em>
                            <h1>
                            <h2>
                            <h3>
                            <i>
                            <img>
                            <kbd>
                            <li>
                            <ol>
                            <p>
                            <pre>
                            <s>
                            <sub>
                            <sup>
                            <strike>
                            <strong>
                            <u>
                            <ul>
                            <br>
                            <hr>';

        protected $db = null;
        public $user = null;
        public $reply;

        public function __construct($db, $userId, $threadId = 0)
        {
            parent::__construct();

            $this->db = $db;

            // Instantiate a user object
            $this->user = new DatabaseObject_User($db);
            $this->user->load($userId);

            // Instantiate a mail object
            $this->mail = new DatabaseObject_Mail($db);
            $this->mail->loadForUser($this->user->getId(), $threadId);
            
            //Zend_Debug::dump($this->mail);die;
            	
            // Instantiate new mail body object
            $this->mailBody = new DatabaseObject_MailBody($db);

            // Instantiate new mail_status object
            $this->mailStatus = new DatabaseObject_MailStatus($db);
        }

        
        public function process(Zend_Controller_Request_Abstract $request)
        {
        	/*
        	* VALIDATE thread_id
        	*/
        	$this->thread_id = (int) $request->threadId;

			if ($this->thread_id <= 0) {
				//$this->addError('mail', 'Invalid thread');
				// Create new threadId
				$this->thread_id = DatabaseObject_Mail::getNextThreadId($this->db,array());
			}
			
        	/*
        	* VALIDATE user
        	*/
			if ($this->recipient != $request->getPost('recipient') ) {
				$this->thread_id = DatabaseObject_Mail::getNextThreadId($this->db,array());
			}

        	if ($this->user_id_to <= 0 || $this->recipient == '') {
				if (!$this->user_id_to = DatabaseObject_User::getUserIdByName($this->db,array('user_name' => $this->recipient))) {
					$this->addError('mail', 'Sorry, that user was not found.');
	        	}
        	}
			
        	/*
        	* VALIDATE mail_subject
        	*/
			if ($this->mail_subject != $request->getPost('mail_subject') ) {
				$this->thread_id = DatabaseObject_Mail::getNextThreadId($this->db,array());
			}

			$this->mail_subject = $this->sanitize($request->getPost('mail_subject'));
        	
        	if (strlen($this->mail_subject) <= 0 || $this->mail_subject == '') {
        		$this->addError('mail_subject', "Please enter a subject for your mail.");
        	}
        	
        	if (strlen($this->mail_subject) > 255) {
        		$this->addError('mail_subject', "That subject is too long, please shorten it.");
        	}
        	
        	$this->mail_subject = $this->cleanHtml($this->mail_subject, self::$tags);

        	/*
        	* VALIDATE mail_body
        	*/
        	$this->mail_body = $request->getPost('mail_body');

        	if (strlen($this->mail_body) <= 0 || trim($this->mail_body == "")) {
        		$this->addError('mail_body', "Don't have much to say, do you? Please enter a message to send.");
        	}
        	if (strlen($this->mail_body) > 65535) {
        		$this->addError('mail_body', "Your message is too long for Yehoodi. Shorten it split it into multiple messages...");
        	}
        	$this->mail_body = $this->cleanHtml($this->mail_body, self::$tags);
        	
        	/*
        	* Record the users IP address
        	*/
        	$ip = ip2long($_SERVER['REMOTE_ADDR']);
        	
        	if (!$this->hasError()) {
        		
        		// Mail Object data...
        		$this->mail->thread_id = $this->thread_id;
        		$this->mail->user_id_from = $this->user->getId();
        		$this->mail->user_id_to = $this->user_id_to;
        		$this->mail->mail_subject = $this->mail_subject;
        		$this->mail->remote_ip = $ip;

        		if($this->mail->save()) {
	        		// Mail Body data...
	        		$this->mailBody->mail_id = $this->mail->getId();
	        		$this->mailBody->mail_body = $this->mail_body;
	        		$this->mailBody->save();
	        		
	        		// Update the mail status
		            $status = new DatabaseObject_MailStatus($this->db);
		            $status->thread_id = $this->thread_id;
		            $status->user_id = $this->user_id_to;
		            $status->mail_status = DatabaseObject_Mail::MAIL_NEW;
		            $status->save();
		            
	            	$this->processNotifications($this->mail->getId());
        			
        		}
	            //TODO: Write this so if anything fails we can rollback the changes and report an error
        	}
        	
        	// return TRUE if no errors
        	return !$this->hasError();
        }

        /**
         * Sends the notices that this mail
         * message has been sent
         *
         * @param int $rsrc_id
         */
        protected function processNotifications($mailId)
        {
        	$users = array(array('user_id' => $this->user->getId()));
        	
        	// Send the notifications
			$mail = new Notifier($this->db, $users);
			$mail->sendMailNotification('user-notify-mail.tpl', $mailId);
        	
        }
    }