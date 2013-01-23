<?php

/**
 * Yehoodi 3.0 AdminController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 
 * Controls all user administrator actions
 *
 */
class AdminController extends CustomControllerAction 
{
	protected $action;
	protected $section;
	
	public function init()
	{
		parent::init();
        $request = $this->_request;

        // Get the page params
    	$this->action = $request->getParam('action');
    	$this->section = $request->getParam('section');

    	// Set up new date time
        $this->dateTime = new DateTime("now", new DateTimeZone(date_default_timezone_get()));
	}

	public function indexAction()
    {
    	$this->view->title = "Admin";
		$this->view->currentPage = 'home';
		
		// Site stats
		$this->view->topicCount = DatabaseObject_Resource::getResourceCount($this->db, array());
		$this->view->commentCount = DatabaseObject_Comment::getCommentCount($this->db, array());
		$this->view->userTotalCount = DatabaseObject_User::GetUsersCount($this->db, array());
		$this->view->userActiveCount = DatabaseObject_User::GetUsersCount($this->db, array('is_active' => 1));
		
        $utility = new Utility();
        $dbSize = $utility->getDBSize();

        $y3StartDate = strtotime('2001-03-28');
        $y2TopicCount = 0;
        $y2CommentCount = 0;
        $boardDays = ( time() - $y3StartDate ) / 86400;
        $avatarDirSize = common::getDirectorySize(Zend_Registry::get('imageConfig')->AvatarPath);
        
        //Zend_Debug::dump($avatarDirSize);die;
        $this->view->dbSize =  sprintf("%.2f", $dbSize[0]['size']);
        $this->view->y3TopicsPerDay = sprintf("%.2f", ($this->view->topicCount - $y2TopicCount) / $boardDays);
        $this->view->y3CommentsPerDay = sprintf("%.2f", ($this->view->commentCount - $y2CommentCount) / $boardDays);
        $this->view->avatarDirSize = common::sizeFormat($avatarDirSize['size']);

        // Who is online?
		$this->view->usersOnline = DatabaseObject_User::getOnlineUsers($this->db, array('time_limit' => $timeOnline));
		$this->view->usersOnlineCount = DatabaseObject_User::getOnlineUsersCount($this->db, array('time_limit' => $timeOnline));

    }

    public function usersAction()
    {
		$this->view->currentPage = 'user';
    	$this->view->title = "Admin";

		switch ($this->section) {
			
			case 'ban':

			    $this->messenger = $this->_helper->_flashMessenger;
				$fp = new FormProcessor_SiteBan($this->db, $banId);
				
		        if ($this->getRequest()->isPost()) {

		        	// Check and see if the user actually did anything...
			        if($this->getRequest()->getPost('user_name') == '' 
		        		&& $this->getRequest()->getPost('email_address') == '' 
		        		&& $this->getRequest()->getPost('remote_ip') == ''
		        		&& !$this->getRequest()->getPost('unban_user_name')
		        		&& !$this->getRequest()->getPost('unban_email_address')
		        		&& !$this->getRequest()->getPost('unban_remote_ip') ) {
	
		        			$this->messenger->addMessage(array('notify' => array('Nothing to do')));
		        			
		        	} else {
			        	// They did something...
			        	
			        	// Process the input boxes
			        	if ($fp->process($this->getRequest())) {
	
			        		// load the new user stuff
							$fp = new FormProcessor_SiteBan($this->db, $banId);
					        
					        // send messages
			        		$this->messenger->addMessage(array('notify' => array('Banlist updated')));
			        		
			        	}
			        	
			        	// Now process the multi list boxes	        		
		                $users =		$this->getRequest()->getPost('unban_user_name');
						$emails =		$this->getRequest()->getPost('unban_email_address');
						$remote_ips =	$this->getRequest()->getPost('unban_remote_ip');
						
						if(count($users)) {
							foreach($users as $value) {
								$userObj = new DatabaseObject_SiteBan($this->db);
								$userObj->load($value);
								$userObj->delete();
							}

							$this->messenger->addMessage(array('notify' => array('Users removed from banlist')));
						}

						if(count($emails)) {
							foreach($emails as $value) {
								$emailObj = new DatabaseObject_SiteBan($this->db);
								$emailObj->load($value);
								$emailObj->delete();
							}

							$this->messenger->addMessage(array('notify' => array('Email addresses removed from banlist')));
						}

						if(count($remote_ips)) {
							foreach($remote_ips as $value) {
								$remoteIPObj = new DatabaseObject_SiteBan($this->db);
								$remoteIPObj->load($value);
								$remoteIPObj->delete();
							}

							$this->messenger->addMessage(array('notify' => array('IP addresses removed from banlist')));
						}
			        		
		        	}
				}

		        $this->view->fp = $fp;
		        $this->view->userName =		DatabaseObject_SiteBan::getBannedUsers($this->db, array());
		        $this->view->emailAddress =	DatabaseObject_SiteBan::getBannedEmails($this->db, array());
		        $this->view->remoteIP =		DatabaseObject_SiteBan::getBannedIPs($this->db, array());
		
		        // send messages to the user
		    	$this->view->messages = $this->_helper->_flashMessenger->getCurrentMessages();
				$this->_helper->_flashMessenger->clearCurrentMessages();

				break;
				
			case 'disallow':
				$this->messenger = $this->_helper->_flashMessenger;
				$fp = new FormProcessor_SiteDisallow($this->db, $disallowId);

		        if ($this->getRequest()->isPost()) {

		        	// Check and see if the user actually did anything...
			        if($this->getRequest()->getPost('ban_user_name') == '' 
		        		&& !$this->getRequest()->getPost('allow_user_name') ) {
	
		        			$this->messenger->addMessage(array('notify' => array('Nothing to do')));
		        			
		        	} else {

			        	// Process the input boxes
			        	if ($fp->process($this->getRequest())) {
	
			        		// load the new user stuff
							$fp = new FormProcessor_SiteDisallow($this->db, $disallowId);
	
					        // send messages
			        		$this->messenger->addMessage(array('notify' => array('Disallow list updated')));
			        	}
	
			        	// Now process the multi list boxes	        		
		                $users =		$this->getRequest()->getPost('allow_user_name');
	
		                if(count($users)) {
							foreach($users as $value) {
								$userObj = new DatabaseObject_SiteDisallow($this->db);
								$userObj->load($value);
								$userObj->delete();
							}
	
							$this->messenger->addMessage(array('notify' => array('Users removed from disallow list')));
						}
		        	}
		        }

		        $this->view->fp = $fp;
		        $this->view->userName =		DatabaseObject_SiteDisallow::getDisallowNames($this->db, array());
		        
		        // send messages to the user
		    	$this->view->messages = $this->_helper->_flashMessenger->getCurrentMessages();
				$this->_helper->_flashMessenger->clearCurrentMessages();
				
				break;
				
			case 'manage_edit':

				$this->messenger = $this->_helper->_flashMessenger;

				$userId = $this->getRequest()->getParam('user_id');
				//$user = DatabaseObject_User::GetUsers($this->db, array('user_id' => $userId));
				
				$fp = new FormProcessor_UserDetails($this->db, $userId);
				
		        if ($this->getRequest()->isPost()) {

		        	// Process the input boxes
		        	if ($fp->process($this->getRequest())) {

		        		// load the new user stuff
						$fp = new FormProcessor_UserDetails($this->db, $userId);
				        
				        // send messages
		        		$this->messenger->addMessage(array('notify' => array('User information updated')));
		        		
		        	}
		        	
		        }
		        
		        $this->view->fp = $fp;
		    	
		        $options = array('user_id'		=> $userId,
		    					 'is_active'	=> DatabaseObject_Resource::STATUS_LIVE
						    	);
		        $this->view->submits = DatabaseObject_Resource::getUserResourceCount($this->db, $options);
		    	$this->view->comments = DatabaseObject_Comment::getUserCommentCount($this->db, $options);

		        // send messages to the user
		    	$this->view->messages = $this->_helper->_flashMessenger->getCurrentMessages();
				$this->_helper->_flashMessenger->clearCurrentMessages();

				break;
				
			case 'permission':
				break;
				
			default:
				// If no other section is chosen, or this is the first click, then show this module.
			    $this->section = 'find_user';
				
			    $options = array('order'			=> 'user_name',
								 'limit'			=> 0,
								 'offset'			=> 0
								 );
		        $this->view->userName =		DatabaseObject_User::getAllUserNames($this->db, $options);
				
				if ($this->getRequest()->isPost()) {
		        	
					if($userName = $this->getRequest()->getPost('input_userName') ) {
						
						if($userId = DatabaseObject_User::getUserIdByName($this->db, array('user_name' => $userName))) {
							$this->_redirect($this->getUrl('users', 'admin') . '?section=manage_edit&user_id=' . $userId );
						}
						
					} elseif ($userId = (int) $this->getRequest()->getPost('input_userId') ) {
						$this->_redirect($this->getUrl('users', 'admin') . '?section=manage_edit&user_id=' . $userId );
					} elseif ($userId = (int) $this->getRequest()->getPost('select_userName') ) {
						$this->_redirect($this->getUrl('users', 'admin') . '?section=manage_edit&user_id=' . $userId );
					}
		        }
				
		        // send messages to the user
		    	$this->view->messages = $this->_helper->_flashMessenger->getCurrentMessages();
				$this->_helper->_flashMessenger->clearCurrentMessages();

				break;
				
		}
    	
        $this->view->section = $this->section;
    	
    	// Render!
        $this->_helper->viewRenderer('users');
    	
    }
    
    public function topicsAction()
    {

		$this->view->currentPage = 'topics';

		switch ($this->section) {
			
			//case 'find_topic':
			
			default:

			    // If no other section is chosen, or this is the first click, then show this module.
			    $this->section = 'find_topic';

			    if ($this->getRequest()->isPost()) {
		        	
					if($userName = $this->getRequest()->getPost('input_userName') ) {
						
						if($userId = DatabaseObject_User::getUserIdByName($this->db, array('user_name' => $userName))) {
							$this->_redirect($this->getUrl('users', 'admin') . '?section=manage_edit&user_id=' . $userId );
						}
						
					} elseif ($userId = (int) $this->getRequest()->getPost('input_userId') ) {
						$this->_redirect($this->getUrl('users', 'admin') . '?section=manage_edit&user_id=' . $userId );
					} elseif ($userId = (int) $this->getRequest()->getPost('select_userName') ) {
						$this->_redirect($this->getUrl('users', 'admin') . '?section=manage_edit&user_id=' . $userId );
					}
		        }
				
		        // send messages to the user
		    	$this->view->messages = $this->_helper->_flashMessenger->getCurrentMessages();
				$this->_helper->_flashMessenger->clearCurrentMessages();
		        
		        break;

    	}

	    $this->view->section = $this->section;
	    
	    // Render!
	    $this->_helper->viewRenderer('topics');

    }
    
    /**
     * The svn update stuff calls a c program that actually does the svn update
     * since the PHP apache user doesn't have access. 
     * 
     * /bin/svn_update_dev
     * /bin/svn_update_stage
     *
     */
    public function siteAction()
    {
		$this->view->currentPage = 'site';
        $this->messenger = $this->_helper->_flashMessenger;
		
        switch ($this->section) {

			//case 'svn':
            default:

                // If no other section is chosen, or this is the first click, then show this module.
			    $this->section = 'svn';
                
				if ($this->getRequest()->isPost()) {
				    
				    $request = $this->getRequest();
				    
				    //Zend_Debug::dump($request->getParam('svn_update'));
				    
				    switch ($request->getParam('svn_update')) {
				        case 'alba':
        				    $cmd = "/bin/svn_update_dev";
        				    break;
    				    
				        case 'calloway':
        				    //$cmd = "/bin/svn_update_stage";
        				    $cmd = "/usr/bin/svn info /var/www/sites/yehoodi3.com/stage/trunk";
        				    //$cmd = "whoami";
        				    break;

				        default:
        				    $cmd = "/bin/svn_update_dev";
        				    break;
				            
				    }
				    
				    $output = exec($cmd, $result);
				    $this->view->svnOutput = $output;
				}

				// send messages to the user
		    	$this->view->messages = $this->_helper->_flashMessenger->getCurrentMessages();
				$this->_helper->_flashMessenger->clearCurrentMessages();
		        
		}
		
		$this->view->section = $this->section;
    	
    	// Render!
        $this->_helper->viewRenderer('site');

    }
    
    protected function my_exec($cmd, $input='') 
    {
        $proc=proc_open($cmd, array(0=>array('pipe', 'r'), 1=>array('pipe', 'w'), 2=>array('pipe', 'w')), $pipes); 
        fwrite($pipes[0], $input);fclose($pipes[0]); 
        $stdout=stream_get_contents($pipes[1]);fclose($pipes[1]); 
        $stderr=stream_get_contents($pipes[2]);fclose($pipes[2]); 
        $rtn=proc_close($proc); 

        return array('stdout'=>$stdout, 
                   'stderr'=>$stderr, 
                   'return'=>$rtn 
                  ); 
    }
}