<?php

/**
 * Yehoodi 3.0 ProfileController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 
 * User Profile
 *
 */
class ProfileController extends CustomControllerAction 
{
	protected $action;
	protected $member;
	
	public $username;
	public $userMeta = nul;

	public function init()
	{
        parent::init();

        // Get the page params
    	$this->action = $this->_request->getParam('action');
        $this->username = $this->_request->getParam('username');

    	// check the url for a valid user
		$userId = DatabaseObject_User::getUserIdByName($this->db, array('user_name' => $this->username));
    	if (!$userId) {
        	$this->_redirect($this->getUrl('index', 'index'));
    	} else {
    		$this->member = new DatabaseObject_User($this->db);
    		$this->member->load($userId);
    		
    		// Is the user active?
    		if(!$this->member->is_active) {
            	$this->_redirect($this->getUrl('index', 'index'));
    		}
    	}

        // Get the user meta info
        $this->userMeta = new DatabaseObject_UserMeta($this->db, $userId);

        // Set up Sphinx Search Client
		$this->maxSearchResults = Zend_Registry::get('searchConfig')->sphinxMaxReturns;
		$sphinxIP = Zend_Registry::get('searchConfig')->sphinxLocation;
		$sphinxPort = (int) Zend_Registry::get('searchConfig')->sphinxPort;
		
        $this->sphinxClient = new SphinxClient();
        $this->sphinxClient->SetServer( $sphinxIP, $sphinxPort);
        $this->sphinxClient->SetConnectTimeout( 30 );

        $this->sphinxClient->SetLimits( 0, 1, $this->maxSearchResults ); // How many records to pull
	} // init

	public function indexAction()
    {
    	$request = $this->getRequest();
    	
    	// assign the user profile object to this var
    	$member = $this->member;
    	
    	// for getting the user counts from sphinx (quick and dirty)
		$userName = $this->member->user_name;
    	$searchQuery = UtilityController::cleanUpUserName($userName);

    	$this->sphinxClient->SetMatchMode( SPH_MATCH_PHRASE );   // Mode set for exact matching the query

        // User - Specific user searches
        // Topics
       	$this->sphinxClient->AddQuery( $searchQuery, 'resource_usercounts' );

       	// Comments
       	$this->sphinxClient->AddQuery( $searchQuery, 'comment_usercounts' );

       	// Events
       	$this->sphinxClient->AddQuery( $searchQuery, 'event_usercounts' );
    	
		$this->sphinxClient->SetArrayResult( true );    // Give me an array when done.

        // Run Query
        $searchResults = $this->sphinxClient->RunQueries();

        //Zend_Debug::dump($searchResults);
        
    	// assign to smarty
    	$this->view->member = $this->member;
    	$this->view->userMeta = $this->userMeta;
    	$this->view->lastVisit = $member->getLastLogin();

        if($member->profile->birthdate < '1901-00-00 00:00:00') {
    	    unset($this->view->birthdate);
    	} else {
    		$this->view->birthdate = common::neatBirthDate($member->profile->birthdate);
    	}

    	// yehoodi stats
    	$options = array('user_id'		=> $member->getId(),
    					 'is_active'	=> DatabaseObject_Resource::STATUS_LIVE
				    	);
    	$this->view->topics = $searchResults[0]['total_found'];
    	$this->view->comments = $searchResults[1]['total_found'];
    	$this->view->events = $searchResults[2]['total_found'];
    	$this->view->votes = DatabaseObject_UserVote::getUserVoteCount($this->db, array('user_id' => $member->getId()));
		$this->view->defaultAvatar = Zend_Registry::get('userConfig')->DefaultAvatar;

        // Assign our top level breadcrumb
        $this->breadcrumbs->addStep($this->member->user_name . ' - User Profile', $this->getUrl( null, 'profile'));

        // Render!
        $this->_helper->viewRenderer('index');

    }
}