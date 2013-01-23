<?php

/**
 * Yehoodi 3.0 MailController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 
 * Controls all user mail actions
 *
 */
class MailController extends CustomControllerAction 
{
	// Vars for pagination
	protected $limit;
	protected $adjacents;
	protected $page;
	protected $offset;
	
	public $box;
	
	protected $mailTotalCount;
	protected $mailUnreadCount;
	protected $mailSentCount;
	
	public function init()
	{
        parent::init();
        // Assign our top level breadcrumb
        $this->breadcrumbs->addStep('Messages', $this->getUrl( null, 'submit'));
        
         // get the user information
        $this->identity = Zend_Auth::getInstance()->getIdentity();

        // get any post/get info
    	$request = $this->getRequest();

    	$this->action = $request->action;
    	$this->order = $request->order;
        $this->page = $request->page;
       	$this->mailId = $this->identity->user_id;
       	$this->box = $request->box;
        
        // This comes from the config.ini
    	$this->limit = Zend_Registry::get('paginationConfig')->MailPerPage;
    	$this->adjacents = Zend_Registry::get('paginationConfig')->Adjacents;
        $this->offset = ($this->page - 1) * $this->limit;

        // set the default record count options
		$this->countOptions = array(
            'user_id_to'	=> $this->mailId,		// filtering on this mailId
            'user_id'		=> $this->mailId,		// filtering on this mailId
            'limit'			=> 0,					// limit number of records
            'offset'		=> 0					// records offet for pagination
		);

        // set the default mail options
        $this->mailOptions = array(
            'user_id_to'	=> $this->mailId,		// filtering on this mailId
            'limit'			=> $this->limit,		// limit number of records
            'offset'		=> $this->offset,		// records offet for pagination
            'order'			=> 'm.mail_date DESC'		// What are we ordering this result set by?
        );

		// set the unread mail count options
		$this->sentMailCountOptions = array(
            'user_id'		=> $this->mailId,		// filtering on this mailId
            'limit'			=> $this->limit,		// limit number of records
            'offset'		=> $this->offset,		// records offet for pagination
            'order'			=> 'm.mail_date DESC'	// What are we ordering this result set by?
		);

		// Get total mail count and Unread mail count
    	$this->mailTotalCount = DatabaseObject_Mail::getAllMailCount($this->db, $this->countOptions);
		$this->mailUnreadCount = DatabaseObject_MailStatus::getNewMailCount($this->db, $this->countOptions);
		//$this->mailSentCount = DatabaseObject_Mail::getSentMailCount($this->db, $this->sentMailCountOptions);

        // this checks if we are on an iPad / iPod / iPhone
        $this->view->smart_device = $this->is_smart_device();
	} // init

	public function indexAction()
    {
    	// Switch based on the box request variable
		switch($this->box) {
    		
    		case 'sent':
		    	// Display all mail sent by the user
				$mailList = DatabaseObject_Mail::getAllSentMail($this->db, $this->sentMailCountOptions);

				$mailCount = $mailSentCount;
				break;		
    		
    		case 'inbox':
    		default:
		    	// Display all mail addressed to the user
				$mailList = DatabaseObject_Mail::getAllMail($this->db, $this->mailOptions);
				
				$mailCount = DatabaseObject_Mail::getAllMailCount($this->db, $this->mailOptions);
				$this->box = 'inbox';
    			break;
    	}
		
        // Invalid page number check
		if ($mailCount > 0) {
        	if ($this->page > ceil($mailCount / $this->limit)) {
	        	$this->_redirect($this->getUrl(null,'mail'));
	        }
		}

    	$this->view->mail = $mailList;
    	$this->view->mailTotalCount = $this->mailTotalCount;
    	$this->view->mailUnreadCount = $this->mailUnreadCount;
    	$this->view->currentTab = $this->box;
    	
	    // send messages to the user
    	$this->view->messages = $this->_helper->_flashMessenger->getMessages();

    	// Pagination
    	$this->view->pageResultNum = $this->limit;
        $this->view->pageNumber = $this->page;
        $this->view->totalResults = $mailCount;
        
        $this->view->order = $this->order;
        if ($mailCount > $this->limit) {
	        $this->getPaginationString($this->page, $mailCount, $this->limit, $this->adjacents, "{$this->getUrl(null,'mail')}","{$this->box}/");
        }

		// Render!
       $this->_helper->viewRenderer('index');
    }

    public function messageAction()
    {
		// vars
		$recipient = null;
        
        // Get the page params
		$params = $this->_request->getParams();
		
		// Get the recipient if any
		if (isset($params['recipient'])) {
		  $recipient = UtilityController::sanitize($params['recipient']);
		}

		// Get the request parameters
    	$request = $this->getRequest();
    	
    	if ($params['id'] > 0) {
    		// Adding to an ongoing discussion
	    	$threadId = (int) $params['id'];
	    	
    		// Make sure this user is allowed to read the email
	    	$mailObj = new DatabaseObject_Mail($this->db);
	    	if (!$mailObj->loadForUser($this->identity->user_id, $threadId)) {
				$this->_redirect($this->getUrl());
			}
	
			// set the default mail options
	        $this->threadOptions = array(
	            'thread_id'		=> $threadId,		// filtering on this mailId
	            'limit'			=> $this->limit,		// limit number of records
	            'offset'		=> $this->offset,		// records offet for pagination
	            'order'			=> 'm.mail_date ASC',		// What are we ordering this result set by?
	            'user_id'		=> $this->mailId
	        );
	    	
			// Get the subject
			$mailSubject = DatabaseObject_Mail::getMailSubjectByThreadId($this->db, $this->threadOptions);
			
	        // Display all messages in the thread
			$mailThread = DatabaseObject_Mail::getMailThread($this->db, $this->threadOptions);
	    	
	    	// instantiate a new Mail object
	    	$fp = new FormProcessor_Mail($this->db, $this->identity->user_id);
	    	$fp->mail_subject = $mailSubject;
			$otherUser = DatabaseObject_Mail::getUserIdOfThreadRecipient($this->db, $threadId);
			$fp->recipient = DatabaseObject_User::getUserNameById($this->db, array('user_id' => $otherUser));
	    	
    	} else {
    		// Brand new discussion
    		
	    	// instantiate a new Mail object
	    	$fp = new FormProcessor_Mail($this->db, $this->identity->user_id);

	    	$fp->recipient = $request->recipient;
    	}
    	    	
        // Save if everything's cool
        if ($this->_request->isPost() && $this->identity) {
			if ($fp->process($request)) {
				// Inform user mail was sent
	        	$this->messenger = $this->_helper->_flashMessenger;
				$this->messenger->addMessage(array('notify' => array('Your message was sent.')));

				// Redirect to Inbox
				$this->_redirect($this->getUrl(null,'mail'));
			} else {
		        // Error on the form
				// User flash messneger to display the error on the current page
		        //$this->messenger->addMessage(array('error' => $fp->getErrors()));
	        	//$this->_redirect($this->getUrl('message','mail').$threadId.'/'.$this->page);
			}
        }
        
        // Assign to Smarty
		$this->view->fp= $fp;
    	$this->view->mailTotalCount = $this->mailTotalCount;
    	$this->view->mailUnreadCount = $this->mailUnreadCount;
    	$this->view->mailSentCount = $this->mailSentCount;
    	$this->view->currentTab = $this->box;

    	$this->view->threadId = $threadId;
    	$this->view->mailMessage = $mailThread;
    	$this->view->mailSubject = $mailSubject;
    	$this->view->recipient = $recipient;

    	// Assign our top level breadcrumb
        $this->breadcrumbs->addStep('Mail - Message: ' . $mailSubject, $this->getUrl( null, 'mail'));

    	// Render!
        $this->_helper->viewRenderer('message');
    }

    
    /**
	 * Catch-all Action for invalid url requests
	 *
	 * @param unknown_type $action
	 * @param unknown_type $arguments
	 */
	function __call($action, $arguments)
    {
		// Invalid controller specified
		// Redirect to the top
		
		$this->_redirect($this->getUrl(null, 'mail'));
		
    }

}