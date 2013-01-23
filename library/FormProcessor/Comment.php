<?php
    class FormProcessor_Comment extends FormProcessor
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
        public $resourceComment;
        protected $_validateOnly = false;

        public function __construct($db, $userId, $commentId = 0)
        {
            parent::__construct();

            $this->db = $db;
            $this->user_id = $userId;

            // Load the user object
            $this->user = new DatabaseObject_User($db);
            $this->user->load($userId);
            
            // Load the comment object
            $this->resourceComment = new DatabaseObject_Comment($db);
            $this->resourceComment->loadForUser(Zend_Auth::getInstance()->getIdentity(), $commentId);

            // If this comment already exists then set the comment property to the comment contents
            if ($this->resourceComment->isSaved()) {
            	$this->comment = $this->resourceComment->comment;
            } else {
            	$this->resourceComment->user_id = $this->user->getId();
            }
        }

        
        public function validateOnly($flag)
        {
            $this->_validateOnly = (bool) $flag;
        }

        public function process(Zend_Controller_Request_Abstract $request)
        {
            // Validate the resource id (comment thread id)
        	$this->rsrc_id = (int) $request->getPost('id');
			
        	// closed topic check
        	$resource = new DatabaseObject_Resource($this->db);
        	$resource->load($this->rsrc_id);
        	if ($resource->isClosed()) {
				$this->addError('commentError', 'You cannot post to a closed topic');
        	}
            
			// post flood checking
		    $memcache = new Memcache;
		    $memcache->connect("localhost",11211); # You might need to set "localhost" to "127.0.0.1"

		    if($memcache->get("key") == 'c_'.$this->user_id) {
				$this->addError('commentError', 'You must wait at least one minute before posting another comment.');
		    }
        	
        	// rsrc_id validate
		    if ($this->rsrc_id <= 0)
				$this->addError('commentError', 'Invalid comment thread');
        	
        	// Validate the comment text
        	
			$this->comment = $request->getPost('comment');
        	$this->comment = $this->cleanHtml($this->comment, self::$tags);
        	
        	if (strlen($this->comment) <= 0 || $this->comment == '') {
        		$this->addError('commentError', 'Don\'t have much to say, do you? Next time please enter a comment.');
        	}

        	if (strlen($this->comment) > 65535) {
        		$this->addError('commentError', 'Seriously...that comment is way too long. Who is this, Mouth? Please shorten it.');
        	}
        	
        	// Options for geting the Comment count
        	$options = array('rsrc_id' => $this->rsrc_id );

        	// this is the user's current IP address
        	$ip = ip2long($_SERVER['REMOTE_ADDR']);
        	
			// Grab hidden fields from the form
			// Hidden reply to id for the ravelry style quote boxes
			$params = explode('_',$request->getPost('replyToId'));
			
			$type = $params[0]; // r for resource, c for comment
			if (isset($params[1])) {
                $id = $params[1]; // the id of the resource or comment
			}
			
			// if the type is a comment then the user is replying directly to a comment in the thread
			if ($type == 'c') {
				$this->reply_to_id = (int) $id;
			}
			
        	if (!$this->_validateOnly && !$this->hasError()) {
        		$this->resourceComment->rsrc_id = $this->rsrc_id;
        		if ($this->resourceComment->isSaved()) {
                    // Copy the old comment to the comment_history table
                    $commentHistory = new DatabaseObject_CommentHistory($this->db);
                    $commentHistory->comment_id = $this->resourceComment->getId();
                    $commentHistory->rsrc_id = $this->resourceComment->rsrc_id;
                    $commentHistory->editor_id = $this->user->getId();
                    $commentHistory->title = $resource->title;
                    $commentHistory->comment = $this->resourceComment->comment;
                    
                    $commentHistory->save();
        		}
                
        		$this->resourceComment->comment = $this->comment;
        		
        		// update the last edited date if this is being edited
        		if($this->resourceComment->isSaved() && !Zend_Auth::getInstance()->getIdentity()->mod) {
        			// Set up new date time
			        $dateTime = new DateTime("now", new DateTimeZone(date_default_timezone_get()));
					$timeNow = $dateTime->format("Y-m-d H:i:s");
        			$this->resourceComment->date_edited = $timeNow;
        		}
        		
        		// If the current comment number is set then we leave it as is...
        		if ($this->resourceComment->comment_num == 0)
        			$this->resourceComment->comment_num = DatabaseObject_Comment::getNextCommentCount($this->db, $options);
        		
        		// If the reply_to_id is set to something, leave it alone
        		if($this->reply_to_id > 0) {
        			$this->resourceComment->reply_to_id = $this->reply_to_id;
        		} else {
        			$this->resourceComment->reply_to_id = $this->resourceComment->reply_to_id;
        		}
        		
        		//$this->resourceComment->date_created = '0000-00-00 00:00:00';
        		$this->resourceComment->edit_user_id = $this->user->getId();
        		$this->resourceComment->is_active = 1;
        		$this->resourceComment->remote_ip = $ip;
				
        		// Don't update the remote_ip if a mod is editing this post
        		if(Zend_Auth::getInstance()->getIdentity()->mod && $this->resourceComment->remote_ip == '') {
        			$this->resourceComment->remote_ip = $ip;
				}
        		
        		if($this->resourceComment->save()) {
	        		// Update comment count in the resource
	        		// Call static method in resource class to update the table
	        		if(!DatabaseObject_Resource::setCountComments($this->db, array('rsrc_id' => $this->rsrc_id,
	        																   'count_comments' => $this->resourceComment->comment_num))) {
						// Log this error
			            if (Zend_Registry::get('serverConfig')->logging == TRUE) {
			        		$message = sprintf('Error updating the resource count_comments field on rsrc_id: %d by user %s',
					                               $this->rsrc_id,
					                               $this->user_name);
			        			$logger = Zend_Registry::get('errorLogger');
					            $logger->notice($message);
			            }
	        		}

	        		if(!DatabaseObject_Resource::setLastCommentId($this->db, array('rsrc_id' => $this->rsrc_id,
	        																   'last_comment_id' => $this->resourceComment->getId()))) {
						// Log this error
			            if (Zend_Registry::get('serverConfig')->logging == TRUE) {
				        		$message = sprintf('Error updating the resource last_comment_id field on rsrc_id: %d by user %s',
						                               $this->rsrc_id,
						                               $this->user_name);
				        			$logger = Zend_Registry::get('errorLogger');
						            $logger->notice($message);
			            }
	        		}

                	// Set up new date time
                    $dateTime = new DateTime("now", new DateTimeZone(date_default_timezone_get()));

                    if(!DatabaseObject_Resource::setDateLastActive($this->db, array('rsrc_id' => $this->rsrc_id,
	        																   'date_last_active' => $dateTime->format("Y-m-d H:i:s")))) {
						// Log this error
			            if (Zend_Registry::get('serverConfig')->logging == TRUE) {
			                    $message = sprintf('Error updating the resource date_last_active field on rsrc_id: %d by user %s',
						                               $this->rsrc_id,
						                               $this->user_name);
				        			$logger = Zend_Registry::get('errorLogger');
						            $logger->notice($message);
			            }
	        		}
	        		
				    // comment flood protection, but not for MODS
					if (!Zend_Auth::getInstance()->getIdentity()->mod) {
		        		$memcache->set("key",'c_'.$this->user_id, false, 60);
					}
					
					// increase post_count in the user table
					DatabaseObject_User::addPostCount($this->db, $this->user_id);

					// send out the notifications that this resource has been commented on
	        		$this->processNotifications($this->rsrc_id);

	        		// Clear the Resource cache keys
					DatabaseObject_Resource::clearResourceCache();
        		}
        		
        		// Now clear them since we don't need them on the form anymore
        		$this->resourceComment->comment = '';
        	}
        	
        	// return TRUE if no errors
        	return !$this->hasError();
        }
        
        /**
         * Sends the notices that this resource
         * has been commented on
         *
         * @param int $rsrc_id
         */
        protected function processNotifications($rsrc_id)
        {
			// get the list of notify users for this resource
        	$options = array('rsrc_id' => $rsrc_id);
        	
        	$notify = new DatabaseObject_UserResourceNotify($this->db);
        	$users = $notify->getNotifyUsers($this->db, $options);
        	
        	// Send the notifications
			$mail = new Notifier($this->db, $users);
			$mail->sendNotification('user-notify-comment.tpl', $rsrc_id);
        	
        }
    }