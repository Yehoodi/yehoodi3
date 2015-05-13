<?php

/**
 * Yehoodi 3.0 IndexController Class
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * 
 * Landing Page
 *
 */
class IndexController extends CustomControllerAction
{
	
	const FEATURES_LIMIT = 4;
	const LINDY_LIMIT = 5;
	const LOUNGE_LIMIT = 12;
	const EVENT_LIMIT = 5;
	const BIZ_LIMIT = 5;
	
	public function init()
	{
        parent::init();
        $request = $this->_request;

        // Assign our top level breadcrumb
        $this->breadcrumbs->addStep('Yehoodi');
        
        // get the user information
        $this->identity = Zend_Auth::getInstance()->getIdentity();

        // Get the page params
    	$this->action = $request->getParam('action');
    	$this->date = $request->getParam('date');
        
	} // init

	public function indexAction()
    {

        // Set up new date time
		if($this->date) {
			$dateTime = new DateTime($this->date, new DateTimeZone(date_default_timezone_get()));
		} else {
			$dateTime = new DateTime("now", new DateTimeZone(date_default_timezone_get()));
		}

		$this->today = $dateTime->format("Y-m-d H:i:s");
		$this->todayShort = $dateTime->format("Y-m-d"); // For the event calendar
		$this->day = $dateTime->format("d");
        $this->view->swingNationLive = false;

		$dateTime->modify('-6 month');
		$this->lastMonth = $dateTime->format("Y-m-d H:i:s");
		
		// Pull latest activity for each module
		
		//
		// Features
		//
		
		$options = array('rsrc_type_id'               =>  Zend_Registry::get('resourceConfig')->featured,
                         'now_date'		              => $this->today,
                         'rsrc_date'				  => $this->lastMonth,
		                 //'date_last_active_start'     => $this->lastMonth,
			             //'date_last_active_end'       => $this->today,										// date range to pass into FROM
		                 //'order'                      => 'date_last_active',
		                 'order'                      => 'rsrc_date',
						 'is_active'                  => DatabaseObject_Resource::STATUS_LIVE,
                         'range'                      => '90days',
						 'limit'                      => self::FEATURES_LIMIT,
                         //'exclude_cat_id'             => array(9,10,11,13,14),
                         //'only_active_events'         => true,
                         'action'                     => 'latestFeatured'
 						 );
		
        $result = DatabaseObject_Resource::getResources($this->db, $options);
        $latestFeatures = $result['topics'];
        $this->view->latestFeatures = $latestFeatures;
		
		
        //
        // Lindy
        //
        $options = array('rsrc_type_id'               =>  Zend_Registry::get('resourceConfig')->lindy,
                         'date_last_active_start'     => $this->lastMonth,
                         'date_last_active_end'       => $this->today,
		                 'order'                      => 'date_last_active',
						 'is_active'                  => DatabaseObject_Resource::STATUS_LIVE,
                         'range'                      => '180days',
						 'limit'                      => self::LINDY_LIMIT ,
                         'action'                     => 'latestLindy',
                         'date_last_active_start'
 						 );
		
        $result = DatabaseObject_Resource::getResources($this->db, $options);
        $latestLindy = $result['topics'];
        $this->view->latestLindy = $latestLindy;
		
        //
        // The Lounge
        //
        $options = array('rsrc_type_id'               =>  Zend_Registry::get('resourceConfig')->lounge,
                         'date_last_active_start'     => $this->lastMonth,
                         'date_last_active_end'       => $this->today,
		                 'order'                      => 'date_last_active',
						 'is_active'                  => DatabaseObject_Resource::STATUS_LIVE,
                         'range'                      => '6months',
						 'limit'                      => self::LOUNGE_LIMIT ,
                         'action'                     => 'latestLounge'
 						 );
		
        $result = DatabaseObject_Resource::getResources($this->db, $options);
        $latestLounge = $result['topics'];
        $this->view->latestLounge = $latestLounge;
		
        //
        // Events
        //
        $options = array('rsrc_type_id'               =>  Zend_Registry::get('resourceConfig')->event,
                         'date_last_active_start'     => $this->lastMonth,
                         'date_last_active_end'       => $this->today,
		                 'order'                      => 'date_last_active',
						 'is_active'                  => DatabaseObject_Resource::STATUS_LIVE,
                         'range'                      => '180days',
						 'limit'                      => self::EVENT_LIMIT ,
                         'action'                     => 'latestEvent'
 						 );
		
        $result = DatabaseObject_Resource::getResources($this->db, $options);
        $latestEvent = $result['topics'];
        $this->view->latestEvent = $latestEvent;
		
        //
        // Biz
        //
        $options = array('rsrc_type_id'               =>  Zend_Registry::get('resourceConfig')->biz,
                         'date_last_active_start'     => $this->lastMonth,
                         'date_last_active_end'       => $this->today,
		                 'order'                      => 'date_last_active',
						 'is_active'                  => DatabaseObject_Resource::STATUS_LIVE,
                         'range'                      => 'AllTime',
						 'limit'                      => self::BIZ_LIMIT ,
                         'action'                     => 'latestBiz'
 						 );
		
        $result = DatabaseObject_Resource::getResources($this->db, $options);
        $latestBiz = $result['topics'];
        $this->view->latestBiz = $latestBiz;
		
		// Get the old "Who Is Online" info
		$this->processWhoIsOnlineInfo();

        // Live Event is Live!
        $date = new DateTime('America/New_York');
        $shows = array(
            '2014-08-22',
            '2014-08-23',
            '2014-08-24',
        );
        $time = $date->format('H:i:s');

        if (in_array($date->format('Y-m-d'), $shows) && $time >= '20:00:00' && $time <= '02:20:00')
        {
            $this->view->liveShow = true;
        }

		// Give up since they didn't set a location
        $this->_helper->viewRenderer('index');
    }
	
	public function processWhoIsOnlineInfo() {
		// Total Topics
		$options = array('action'        => $this->action
		                );
		$topicTotal = DatabaseObject_Resource::getResourceCount($this->db, $options);
		
		// Total Comments
		$commentTotal = DatabaseObject_Comment::getCommentCount($this->db, array());
		
		// Total Users (Active)
		$userTotal = DatabaseObject_User::GetUsersCount($this->db, array('is_active' => 1));
		
		// Our newest user
		$newestUser = DatabaseObject_User::getNewestUser($this->db, array());

		// Grab the date for the online users info
		$dateTime = new DateTime("now", new DateTimeZone(date_default_timezone_get()));
		$birthDate = $dateTime->format("m-d");

		$dateTime->modify('-5 minutes');
		$timeOnline = $dateTime->format("Y-m-d H:i:s");

		// Who is online?
		$usersOnline = DatabaseObject_User::getOnlineUsers($this->db, array('time_limit' => $timeOnline));
		$usersOnlineCount = DatabaseObject_User::getOnlineUsersCount($this->db, array('time_limit' => $timeOnline));
		
		// Birthdays to celebrate!
		$birthdays = DatabaseObject_User::getBirthdays($this->db,array('birth_date' => $birthDate));
		
		
		// Smarty assign
		$this->view->topicTotal = $topicTotal;
		$this->view->commentTotal = $commentTotal;
		$this->view->userTotal = $userTotal;
		
		$this->view->newestUser = $newestUser;
		
		$this->view->usersOnlineTotal = $usersOnlineCount;
		$this->view->usersOnline = $usersOnline;
		$this->view->userInvisibleCount = $usersOnlineCount - count($usersOnline);
		
		$this->view->birthdays = $birthdays;
		
		//Zend_Debug::dump($birthdays);die;
	}
}