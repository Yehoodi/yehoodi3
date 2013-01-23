<?php
/**
 * Yehoodi 3.0 AccountController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 
 * Controls all user account actions
 *
 */
class AccountController extends CustomControllerAction
{
    public $user;
    public $userId;
    
	public function init()
    {
        parent::init();

        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
        	$this->userId = $auth->getIdentity()->user_id;

        	// Load the user object
        	$this->userObj = new DatabaseObject_User($this->db);
        	$this->userObj->load($this->userId);
        }

    } // init

    public function indexAction()
    {
		// just go to the index page
    	$this->_redirect('/');
    } // indexAction

    public function summaryAction()
    {
		// get the page params
        $request = $this->_request;
    	$this->view->actionType = $request->getParam('view');
    	$rsrc_id = $request->getParam('rsrc_id');
    	
    	// now get data for the view type (if any)
    	switch ($this->view->actionType) {
    		case 'bookmarks':
    			// this will set the resources variable in the smarty template
    			$this->viewAllBookmarks();
    			break;

    		case 'watched':
    			// this will set the resources variable in the smarty template
    			$this->viewAllWatched();
    			break;
    			
    		case 'ignored':
    			// this will set the resources variable in the smarty template
    			$this->viewAllIgnored();
    			break;
    			
    		case 'drafts':
    			// delete the clicked draft
    			if($rsrc_id > 0) {
    				$options = array('user_id' => $this->userId,
    								 'rsrc_id' => $rsrc_id);
    								 
    				if(DatabaseObject_Resource::deleteDraft($this->db, $options)) {
		                $this->messenger = $this->_helper->_flashMessenger;
		        		$this->messenger->addMessage(array('notify' => array('Draft deleted.')));
    				} else {
		                $this->messenger = $this->_helper->_flashMessenger;
		        		$this->messenger->addMessage(array('error' => array('Oops! Draft could not be deleted.')));
    				}
    			}
    			
    			// this will set the resources variable in the smarty template
    			$this->viewAllDrafts();
    			break;
    			
    		default:
    			// not super neccessary, but clear.
    			$this->view->actionType = '';
    			break;
    	}
    	
		$this->view->title = 'Summary';
        $this->view->currentPage = 'summary';
		$this->view->user = $this->userObj;
		
		$this->view->userVotes = DatabaseObject_UserVote::getUserVoteCount($this->db, array('user_id' => $this->userId));
		$this->view->userIgnore = DatabaseObject_UserIgnore::getUserIgnoreCount($this->db, array('user_id' => $this->userId));
		
        $options = array('user_id'		=> $this->userId,
    					 'is_active'	=> DatabaseObject_Resource::STATUS_LIVE
				    	);
		$this->view->userSubmitted = DatabaseObject_Resource::getUserResourceCount($this->db, $options);
		$this->view->userComments = DatabaseObject_Comment::getUserCommentCount($this->db, $options);
		$this->view->userDrafts = DatabaseObject_Resource::getUserDraftCount($this->db, array('user_id' => $this->userId));
		
		$this->view->userBookmarks = DatabaseObject_UserBookmark::getUserBookmarkCount($this->db, array('user_id' => $this->userId));
		$this->view->userWatches = DatabaseObject_UserResourceNotify::getUserNotifyCount($this->db, array('user_id' => $this->userId));
    
        // send messages to the user
    	if($this->_helper->_flashMessenger->getMessages()) {
        	$this->view->messages = $this->_helper->_flashMessenger->getMessages();
    	}
    	if($this->_helper->_flashMessenger->getCurrentMessages()) {
    		$this->view->messages = $this->_helper->_flashMessenger->getCurrentMessages();
    	}
		$this->_helper->_flashMessenger->clearCurrentMessages();

        // Assign our top level breadcrumb
        $this->breadcrumbs->addStep('Summary - Your Account', $this->getUrl( null, 'account'));

    	// Render!
        $this->_helper->viewRenderer('summary');
    } // indexAction

	/**
	 * Renders the users bookmarks
	 * (no pagination)
	 *
	 */
	public function viewAllBookmarks()
	{
		// ** Bookmarks **
	
		$options = array('user_id' => $this->userId
						 );
		$bookmarkIds = DatabaseObject_UserBookmark::getAllBookmarksByUserId($this->db, $options);
		
		if (count($bookmarkIds) > 0) {
			$options = array('order'	=> 'rsrc_id DESC',
							 'rsrc_id'	=> $bookmarkIds,
							 'limit'	=> 0,
							 'offset'	=> 0
							 );
			$bookmarkedResources = DatabaseObject_Resource::getResourceById($this->db, $options);
	
			// set them into the view
			$this->view->resources = $bookmarkedResources;
			$this->view->totalBookmarked = count($bookmarkIds);
		}
	}    
    
    /**
     * Renders the users watched resources
     * 
     *
     */
    public function viewAllWatched()
    {
		// ** Watched Activity **

		$options = array('user_id' => $this->userId
						 );
		$watchedIds = DatabaseObject_UserResourceNotify::getAllNotifyByUserId($this->db, $options);
		
		if (count($watchedIds) > 0) {
			$options = array('order'	=> 'rsrc_date DESC',
							 'rsrc_id'	=> $watchedIds,
							 'limit'	=> 0,
							 'offset'	=> 0
							 );
			$watchedResources = DatabaseObject_Resource::getResourceById($this->db, $options);
	
			$this->view->resources = $watchedResources;
			$this->view->totalWatched = count($watchedIds);
		}
    }

    /**
     * Renders the users ignore list
     * 
     *
     */
    public function viewAllIgnored()
    {
		// ** Ignored Users **

		$options = array('user_id' => $this->userId
						 );
		$ignoredIds = DatabaseObject_UserIgnore::getIgnoredUsersByUserId($this->db, $options);
		
		if (count($ignoredIds) > 0) {
			$options = array('order'			=> 'user_name',
							 'ignored_user_id'	=> $ignoredIds,
							 'limit'			=> 0,
							 'offset'			=> 0
							 );
			$ignoredUsers = DatabaseObject_User::GetUsers($this->db, $options);
	
			$this->view->users = $ignoredUsers;
			$this->view->totalIgnored = count($ignoredUsers);
		}
    }

    /**
     * Renders the users draft resources
     * 
     *
     */
    public function viewAllDrafts()
    {
		// ** Drafts **

		$options = array('user_id' => $this->userId
						 );
		$draftIds = DatabaseObject_Resource::getAllDraftsByUserId($this->db, $options);
		
		if (count($draftIds) > 0) {
			$options = array('order'	=> 'rsrc_date DESC',
							 'rsrc_id'	=> $draftIds,
							 'limit'	=> 0,
							 'offset'	=> 0
							 );
			$draftResources = DatabaseObject_Resource::getResourceById($this->db, $options);
	
			$this->view->resources = $draftResources;
			$this->view->totalDrafts = count($draftIds);
		}
    }

    /**
     * handles the registration of users to the site
     *
     */
    public function registerAction()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
    	
        if($identity) {
        	$this->_redirect($this->getUrl('summary','account'));
        }
    	
		// Create hidden_token if empty
        if (is_null($this->_request->getPost('hidden_token'))) {
    		$hidden_token = $this->generateToken();
		}

		// get any post/get info
    	$request = $this->getRequest();

        // instantiate a new UserRegistration object
    	$fp = new FormProcessor_UserRegistration($this->db);
    	
    	// check if the request camefrom XMLHttpRequest (ajax)
    	// or not
        $validate = $request->isXmlHttpRequest();

        if ($request->isPost()) {
    		// Get our hidden_token
	    	$hidden_token = Zend_Filter::filterStatic($this->getRequest()->getPost('hidden_token'), 'StripTags');
    		
    		// Check Token for form validity
	    	if(!$this->tokenCheck($hidden_token)) {
	            // The token is INVALID. Log this error
	            if (Zend_Registry::get('serverConfig')->logging == TRUE) {
		    		$message = sprintf('Invalid token on REGISTRATION page from %s.',
		                               $_SERVER['REMOTE_ADDR']);
		
		            $logger = Zend_Registry::get('errorLogger');
		            $logger->notice($message);
            }
		        // send error to the next screen
				$this->messenger = $this->_helper->_flashMessenger;
	            $this->messenger->addMessage(array('error' => array('There was an error adding your account information. Please try again.')));
  	            
	            // redirect
	            // TODO: this never redirect. Figure it out...
	            $this->_redirect($this->getUrl('register'));
	    	}
            
	    	if ($validate) {
                $fp->validateOnly(true);
                $fp->process($request);
            }
            else if ($fp->process($request)) {
                $session = new Zend_Session_Namespace('registration');
                $session->user_id = $fp->user->getId();
                $this->_redirect($this->getUrl('registercomplete'));
            }
        }

		if ($validate) {
			if ($fp->getErrors()) {
				$json = array(
						'errors' => $fp->getErrors()
						);
			} else {
				//user's submission validates, allow the user to register
				$json = array();
			}
			$this->sendJson($json);
		} else {
    	    // send messages to the user
        	$this->view->messages = $this->_helper->_flashMessenger->getMessages();

        	$this->breadcrumbs->addStep('Create an Account');
            $this->view->fp = $fp;
	    	$this->view->hidden_token = $hidden_token;
	    	$this->view->section = 'register';
        }
    } // registerAction

    /**
     * Registration is complete and this is run
     *
     */
    public function registercompleteAction()
    {
        // retrieve the same session namespace used in register
        $session = new Zend_Session_Namespace('registration');
        
        // set the session equal FOUR hours. That ought to be enough time.
        $session->setExpirationSeconds(14400);

        // load the user record based on the stored user ID
        $user = new DatabaseObject_User($this->db);
        if (!$user->load($session->user_id)) {
            $this->_forward('register');
            return;
        }

        $this->breadcrumbs->addStep('Create an Account',$this->getUrl('register'));
        $this->breadcrumbs->addStep('Account Created');
        $this->view->user = $user;
    }//registercompleteAction

    /**
     * Log in the current user
     *
     */
    public function loginAction()
    {
    	// if a user's already logged in, send them to the home page
        $auth = Zend_Auth::getInstance();

        if ($auth->hasIdentity())
	    	$this->_redirect('/');
            //$this->_redirect('/account');

        // get the requests
        $request = $this->getRequest();

        // determine the page the user was originally trying to request
        $redirect = $request->getParam('redirect');
        if (strlen($redirect) == 0)
            $redirect = $request->getServer('REQUEST_URI');
        if (strlen($redirect) == 0)
            //$redirect = $this->getUrl();
            $redirect = $this->_redirect('/');

        // initialize errors
        $errors = array();

        // This page should never be requested via XmlHttpRequest (ajax) and therefore
        // this is probably a session timeout on a form. Send them to the login page.
        if($request->isXmlHttpRequest()) {
        	echo "invalid user";
        	exit;
        }
        
        // process login if request method is post
        if ($request->isPost()) {

            // fetch login details from form and validate them
            $username = $request->getPost('user_name');
            $password = $request->getPost('password');

            if (strlen($username) == 0)
                $errors['user_name'] = 'Required username field must not be blank';
            if (strlen($password) == 0)
                $errors['password'] = 'Required password field must not be blank';

            if (count($errors) == 0) {

                // setup the authentication adapter
                $adapter = new Zend_Auth_Adapter_DbTable($this->db,
                                                         'user',
                                                         'user_name',
                                                         'password',
                                                         'md5(?) AND is_active = 1');

                $adapter->setIdentity($username);
                $adapter->setCredential($password);

                // try and authenticate the user
                //Zend_Debug::dump($adapter);die;
                $result = $auth->authenticate($adapter);

                if ($result->isValid()) {
                    $user = new DatabaseObject_User($this->db);
                    $user->load($adapter->getResultRowObject()->user_id);

			        // record login attempt
                    $user->loginSuccess();

                    // create identity data and write it to session
                    $identity = $user->createAuthIdentity();
                    $auth->getStorage()->write($identity);
                    
                    // set the autoLogin cookie
					if($request->getPost('remember') == 1) {
                        $user->setAutologinCookie();
					}

                    // send user to page they originally request
                    $this->_redirect($redirect);
                }

                // record failed login attempt
                DatabaseObject_User::LoginFailure($user_name,
                                                  $result->getCode());
                $errors['user_name'] = 'Your login details were invalid';
            }
        }

        $this->view->errors = $errors;
        $this->view->redirect = $redirect;
    	$this->view->section = 'login';
    } // loginAction

    /**
     * Logs out the current user
     *
     */
    public function logoutAction()
    {
		// kill the current autoLogin cookie if any
    	$user = new DatabaseObject_User($this->db);
		
		if($user->load(Zend_Auth::getInstance()->getIdentity()->user_id)) {
			$user->deleteAutologinCookie();
		}
       
    	Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect($this->getUrl('login'));
    }

    public function fetchpasswordAction()
    {
        // if a user's already logged in, send them to their account home page
        if (Zend_Auth::getInstance()->hasIdentity())
            $this->_redirect($this->getUrl());
            //$this->_redirect('/account');

        $errors = array();

        $action = $this->getRequest()->getQuery('action');

        if ($this->getRequest()->isPost()) {
            $action = 'submit';
        }

        switch ($action) {
            case 'submit':
                $username = trim($this->getRequest()->getPost('user_name'));
                if (strlen($username) == 0) {
                    $errors['user_name'] = 'Required field must not be blank';
                }
                else {
                    $user = new DatabaseObject_User($this->db);
                    if ($user->load($username, 'user_name')) {
                        $user->fetchPassword();
                        $url = $this->getUrl('fetchpassword') . '?action=complete';
                        //$url = '/account/fetchpassword?action=complete';
                        $this->_redirect($url);
                    }
                    else
                        $errors['user_name'] = 'Specified user not found';
                }
                break;

            case 'complete':
                // nothing to do
                break;

            case 'confirm':
                $id = $this->getRequest()->getQuery('id');
                $key = $this->getRequest()->getQuery('key');

                $user = new DatabaseObject_User($this->db);
                if (!$user->load($id))
                    $errors['confirm'] = 'Error confirming new password';
                else if (!$user->confirmNewPassword($key))
                    $errors['confirm'] = 'Error confirming new password';

                break;
        }

        $this->breadcrumbs->addStep('Login', $this->getUrl('login'));
        $this->breadcrumbs->addStep('Fetch Password');
        $this->view->errors = $errors;
        $this->view->action = $action;
    }

    /**
     * Process the Update Your Details page
     *
     */
    public function detailsAction()
    {
        $auth = Zend_Auth::getInstance();

        $fp = new FormProcessor_UserDetails($this->db,
                                            $this->userId);

        if ($this->getRequest()->isPost()) {
            if ($fp->process($this->getRequest())) {
                $auth->getStorage()->write($fp->user->createAuthIdentity());
				
		        $user = new DatabaseObject_User($this->db);
		        $user->load(Zend_Auth::getInstance()->getIdentity()->user_id);

				// load the new user stuff
		        $fp = new FormProcessor_UserDetails($this->db,
		                                            $this->userId);
		        // send messages
                $this->messenger = $this->_helper->_flashMessenger;
        		$this->messenger->addMessage(array('notify' => array('Details Updated')));
            }
        }

        $this->view->fp = $fp;
        $this->view->currentPage = 'details';
        $this->view->user = $this->userObj;

        // Assign our top level breadcrumb
        $this->breadcrumbs->addStep('Details - Your Account', $this->getUrl( null, 'account'));

        // send messages to the user
    	$this->view->messages = $this->_helper->_flashMessenger->getCurrentMessages();
		$this->_helper->_flashMessenger->clearCurrentMessages();
    }

    /**
     * Display the user's current avatar and let them
     * upload a new one if they like
     *
     */
    public function avatarAction()
    {
        // get a user object
    	$auth = Zend_Auth::getInstance();

        // instantiate a form object for the avatar
    	$fp = new FormProcessor_UserAvatar($this->db, $this->userId);

        if ($this->getRequest()->isPost()) {
            if ($this->getRequest()->getParam('formAction') == 'Upload Avatar') {
            	if ($fp->process($this->getRequest())) {
					$this->messenger = $this->_helper->_flashMessenger;
            		$this->messenger->addMessage(array('notify' => array('Avatar Uploaded')));
                    //$this->_redirect($this->getUrl('detailscomplete'));
            	} else {
            		foreach ($fp->getErrors() as $error ){
            			//$this->messenger->addMessage($error);
            		}
            	}
            } elseif ($this->getRequest()->getParam('formAction') == 'Delete Avatar') {
            	$avatar_id = (int) $this->getRequest()->getPost('avatar');
            	$avatar = new DatabaseObject_UserAvatar($this->db);
            	if ($avatar->loadAvatarForUser($this->userId, $avatar_id)) {
            		$avatar->delete();

            		$fp = new FormProcessor_UserAvatar($this->db, $this->userId);
					$this->messenger = $this->_helper->_flashMessenger;
            		$this->messenger->addMessage(array('notify' => array('Avatar Deleted')));
            	}
            }
        }

        $this->view->fp = $fp;
        $this->view->currentPage = 'avatar';
        $this->view->user = $this->userObj;

        // Assign our top level breadcrumb
        $this->breadcrumbs->addStep('Avatar - Your Account', $this->getUrl( null, 'account'));

        // send messages to the user
    	$this->view->messages = $this->_helper->_flashMessenger->getCurrentMessages();
		$this->_helper->_flashMessenger->clearCurrentMessages();
    }

    /**
     * Process the Update site config page
     *
     */
    public function settingsAction()
    {
        $auth = Zend_Auth::getInstance();

        $fp = new FormProcessor_UserDetails($this->db,
                                            $this->userId);

        if ($this->getRequest()->isPost()) {
            if ($fp->process($this->getRequest())) {
                $auth->getStorage()->write($fp->user->createAuthIdentity());

		        $user = new DatabaseObject_User($this->db);
		        $user->load(Zend_Auth::getInstance()->getIdentity()->user_id);

				// load the new user stuff
		        $fp = new FormProcessor_UserDetails($this->db,
		                                            $this->userId);

		        // send messages
                $this->messenger = $this->_helper->_flashMessenger;
        		$this->messenger->addMessage(array('notify' => array('Site Settings Updated')));
            }
        }

        $this->view->fp = $fp;
        $this->view->currentPage = 'settings';
        $this->view->user = $this->userObj;

        // Assign our top level breadcrumb
        $this->breadcrumbs->addStep('Site Settings - Your Account', $this->getUrl( null, 'account'));

        // send messages to the user
    	$this->view->messages = $this->_helper->_flashMessenger->getCurrentMessages();
		$this->_helper->_flashMessenger->clearCurrentMessages();
    }
}